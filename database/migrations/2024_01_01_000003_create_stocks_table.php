<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Миграция для создания таблицы складских остатков
 *
 * @author Yan Bubenok <yan@bubenok.com>
 */
return new class extends Migration
{
    /**
     * Выполнить миграцию
     */
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique()->comment('Внешний ID из API');
            $table->date('stock_date')->index()->comment('Дата данных об остатках');
            $table->string('article')->index()->comment('Артикул товара');
            $table->string('barcode')->nullable()->comment('Штрихкод');
            $table->string('warehouse')->nullable()->index()->comment('Склад');
            $table->integer('quantity')->default(0)->comment('Количество на складе');
            $table->integer('quantity_full')->default(0)->comment('Полное количество');
            $table->string('category')->nullable()->comment('Категория');
            $table->string('subject')->nullable()->comment('Предмет');
            $table->string('brand')->nullable()->comment('Бренд');
            $table->json('additional_data')->nullable()->comment('Дополнительные данные из API');
            $table->timestamps();
            $table->softDeletes();

            // Составные индексы для оптимизации запросов
            $table->index(['article', 'warehouse']);
            $table->index(['stock_date', 'article']);
        });
    }

    /**
     * Откатить миграцию
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
