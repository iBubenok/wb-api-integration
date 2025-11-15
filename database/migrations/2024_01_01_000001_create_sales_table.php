<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Миграция для создания таблицы продаж
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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique()->comment('Внешний ID из API');
            $table->date('sale_date')->index()->comment('Дата продажи');
            $table->dateTime('sale_datetime')->nullable()->comment('Дата и время продажи');
            $table->string('order_number')->nullable()->index()->comment('Номер заказа');
            $table->string('article')->nullable()->index()->comment('Артикул товара');
            $table->string('barcode')->nullable()->comment('Штрихкод');
            $table->decimal('price', 10, 2)->default(0)->comment('Цена');
            $table->decimal('discount', 10, 2)->default(0)->comment('Скидка');
            $table->decimal('final_price', 10, 2)->default(0)->comment('Итоговая цена');
            $table->integer('quantity')->default(1)->comment('Количество');
            $table->string('warehouse')->nullable()->comment('Склад');
            $table->string('region')->nullable()->comment('Регион');
            $table->json('additional_data')->nullable()->comment('Дополнительные данные из API');
            $table->timestamps();
            $table->softDeletes();

            // Составные индексы для оптимизации запросов
            $table->index(['sale_date', 'article']);
            $table->index(['order_number', 'article']);
        });
    }

    /**
     * Откатить миграцию
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
