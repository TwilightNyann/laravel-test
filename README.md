# Smartphone Product Manager API

Простий RESTful API на Laravel 13 та PHP 8.4 для управління каталогом смартфонів. Дозволяє переглядати, створювати, оновлювати та видаляти товари, а також імпортувати дані з зовнішнього сервісу DummyJSON.

### Структура директорій проєкту ###
```
/
├── app/ - основна логіка застосунку (Models, Controllers, Services, Requests, Resources)
├── bootstrap/ - ініціалізація та глобальна обробка винятків (app.php)
├── database/ - міграції бази даних та фабрики (factories)
├── docker/ - конфігурації Docker (nginx, php)
├── tests/ - функціональні та інтеграційні тести
├── .env.example - шаблон змінних оточення
├── composer.json - залежності проєкту та вимога PHP 8.4
├── docker-compose.yml - конфігурація для запуску в Docker
└── README.md - документація проєкту
```

### Технічний стек ###
- PHP: 8.4 (явно зафіксовано в composer.json як ^8.4; у контейнері працює 8.4.22)
- Laravel: 13.18.0 (актуальна стабільна гілка 13.x)
- База даних: PostgreSQL 16 у Docker; SQLite in-memory підтримується для автотестів
- Оточення: Docker та Docker Compose (PHP-FPM, Nginx, PostgreSQL)
- Стиль коду: Laravel Pint (стандарти PSR-12 / Laravel)

### Схема бази даних (products) ###
| Поле | Тип | Індекси / Обмеження | Архітектурне обґрунтування |
| :--- | :--- | :--- | :--- |
| id | bigint | PK, Auto-increment | Стандартний первинний ключ Laravel |
| external_id | integer | nullable, unique | Справжнє обмеження UNIQUE в PostgreSQL для гарантії ідемпотентності методу upsert() без дублікатів |
| title | varchar(255) | required | Назва товару |
| description | text | nullable | Детальний опис товару |
| brand | varchar | nullable, index | Індекс для швидкого пошуку. В коді використовується вираз whereRaw('LOWER(brand) = ?', ...) для сумісності з різними БД |
| category | varchar | required | Категорія товару (наприклад, smartphones) |
| price | decimal(10,2) | required, min:0 | Точна фінансова арифметика без похибок плаваючої коми |
| discount_percentage | decimal(5,2) | nullable | Відсоток знижки |
| rating | decimal(3,2) | nullable | Рейтинг товару від 0.00 до 5.00 |
| stock | integer | default(0) | Залишок на складі |
| sku | varchar | nullable, unique | Справжнє обмеження UNIQUE для унікального артикула товару |
| thumbnail | varchar | nullable | URL мініатюри (валідується як URL) |
| images | json | nullable | JSON-масив URL-адрес фотографій. У моделі кастується через $casts = ['images' => 'array'] |

### Архітектурні рішення ###
- Контролери: Тонкі контролери (`ProductController`), які лише приймають запити та повертають ресурси. Використовують автоматичний роут-біндинг (`Product $product`) без ручних запитів до БД.
- Сервіси: Ізольована бізнес-логіка. `DummyJsonClient` отримує зовнішні дані з захистом від зависання (таймаут 15с) та повторними спробами. `ProductSeeder` мапить та зберігає дані без дублікатів через `upsert()`.
- Валідація: Явний шар валідації (`ListProductsRequest`, `StoreProductRequest`, `UpdateProductRequest`). Сувора перевірка параметрів пагінації (`page`, `limit`) із поверненням статусу 422 при некоректних даних.
- Ресурси API: Єдиний формат JSON-відповідей (`ProductResource`). Усі ендпоінти читання та запису повертають однакову структуру з обгорткою `data`.
- Обробка винятків: Налаштована централізовано в `bootstrap/app.php`. Автоматично перехоплює помилки відсутності моделі та повертає коректні JSON-відповіді зі статусом 404.

### Інструкція з запуску ###
Для запуску сервера необхідно підняти контейнери та ініціалізувати оточення:

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

Для завантаження (сідінгу) товарів із зовнішнього API DummyJSON:

```bash
curl -X POST http://localhost:8080/api/products/seed
```

Адреса API: http://localhost:8080/api/products

### Команди Docker ###
Вхід усередину контейнера з бекендом:

```bash
docker compose exec app bash
```

Перевірка статусу та версії Nginx:

```bash
docker compose exec web nginx -t
docker compose exec web nginx -V
```

Перезавантаження конфігурації Nginx:

```bash
docker compose exec web nginx -s reload
```

Зупинка контейнерів:

```bash
docker compose down
```

### Часті команди всередині контейнера бекенду ###
Запуск міграцій бази даних:

```bash
docker compose exec app php artisan migrate
```

Повний перезапуск міграцій (чиста база даних):

```bash
docker compose exec app php artisan migrate:fresh
```

Запуск лінтера для перевірки та виправлення стилю коду (Laravel Pint):

```bash
docker compose exec app ./vendor/bin/pint
```

Очищення кешу застосунку:

```bash
docker compose exec app php artisan optimize:clear
```

### Таблиця ендпоінтів API ###
Усі ендпоінти повертають консистентний формат JSON з обгорткою data (або метаданими пагінації):

| Метод | Ендпоінт | Опис | Приклад відповіді (скорочено) |
| :--- | :--- | :--- | :--- |
| GET | /api/products?brand=apple&limit=5&page=1 | Пагінований список товарів із фільтром за брендом | {"data": [{"id": 1, "title": "iPhone 9", ...}], "meta": {"total": 16, ...}} |
| POST | /api/products | Створення нового товару | {"data": {"id": 17, "title": "iPhone 15", "price": 999.99, ...}} (Статус 201) |
| GET | /api/products/{id} | Отримання детальної інформації про товар за ID | {"data": {"id": 17, "title": "iPhone 15", ...}} (Статус 404, якщо не знайдено) |
| PATCH | /api/products/{id} | Часткове оновлення даних товару | {"data": {"id": 17, "price": 899.99, ...}} |
| DELETE | /api/products/{id} | Видалення товару | Порожнє тіло відповіді (Статус 204) |
| POST | /api/products/seed | Імпорт товарів з DummyJSON | {"imported": 16, "updated": 0} |

### Запуск тестів ###
Виконання всього набору тестів всередині контейнера:

```bash
docker compose exec app php artisan test
```

### Робота з AI  ###
Під час розробки AI-асистент допомагав з boilerplate кодом.
