<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('external_id')->nullable()->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('brand')->nullable()->index();
            $table->string('category');
            $table->decimal('price', 10, 2);
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->string('sku')->nullable()->unique();
            $table->string('thumbnail')->nullable();
            $table->json('images')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
