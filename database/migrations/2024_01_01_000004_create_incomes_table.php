<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Миграция для создания таблицы поступлений
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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique()->comment('Внешний ID из API');
            $table->date('income_date')->index()->comment('Дата поступления');
            $table->dateTime('income_datetime')->nullable()->comment('Дата и время поступления');
            $table->string('article')->index()->comment('Артикул товара');
            $table->string('barcode')->nullable()->comment('Штрихкод');
            $table->integer('quantity')->default(0)->comment('Количество');
            $table->decimal('price', 10, 2)->default(0)->comment('Цена');
            $table->string('warehouse')->nullable()->index()->comment('Склад');
            $table->string('supply_number')->nullable()->index()->comment('Номер поставки');
            $table->string('status')->nullable()->comment('Статус поступления');
            $table->json('additional_data')->nullable()->comment('Дополнительные данные из API');
            $table->timestamps();
            $table->softDeletes();

            // Составные индексы для оптимизации запросов
            $table->index(['income_date', 'article']);
            $table->index(['supply_number', 'article']);
        });
    }

    /**
     * Откатить миграцию
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
