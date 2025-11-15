<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Миграция для создания таблицы заказов
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique()->comment('Внешний ID из API');
            $table->date('order_date')->index()->comment('Дата заказа');
            $table->dateTime('order_datetime')->nullable()->comment('Дата и время заказа');
            $table->string('order_number')->unique()->index()->comment('Номер заказа');
            $table->string('article')->nullable()->index()->comment('Артикул товара');
            $table->string('barcode')->nullable()->comment('Штрихкод');
            $table->decimal('price', 10, 2)->default(0)->comment('Цена');
            $table->decimal('discount', 10, 2)->default(0)->comment('Скидка');
            $table->integer('quantity')->default(1)->comment('Количество');
            $table->string('status')->nullable()->index()->comment('Статус заказа');
            $table->string('warehouse')->nullable()->comment('Склад');
            $table->string('region')->nullable()->comment('Регион');
            $table->string('customer_name')->nullable()->comment('Имя покупателя');
            $table->string('customer_phone')->nullable()->comment('Телефон покупателя');
            $table->json('additional_data')->nullable()->comment('Дополнительные данные из API');
            $table->timestamps();
            $table->softDeletes();

            // Составные индексы для оптимизации запросов
            $table->index(['order_date', 'status']);
            $table->index(['order_date', 'article']);
        });
    }

    /**
     * Откатить миграцию
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
