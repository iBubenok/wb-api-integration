<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Миграция для создания таблицы логов синхронизации
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
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type')->index()->comment('Тип сущности (sales, orders, stocks, incomes)');
            $table->dateTime('started_at')->index()->comment('Время начала синхронизации');
            $table->dateTime('finished_at')->nullable()->comment('Время завершения синхронизации');
            $table->string('status')->index()->comment('Статус синхронизации (running, completed, failed)');
            $table->integer('records_processed')->default(0)->comment('Количество обработанных записей');
            $table->integer('records_created')->default(0)->comment('Количество созданных записей');
            $table->integer('records_updated')->default(0)->comment('Количество обновленных записей');
            $table->integer('records_failed')->default(0)->comment('Количество неудачных записей');
            $table->json('error_details')->nullable()->comment('Детали ошибок');
            $table->json('metadata')->nullable()->comment('Дополнительные метаданные');
            $table->timestamps();

            // Индексы для быстрого поиска логов
            $table->index(['entity_type', 'status']);
            $table->index(['started_at', 'entity_type']);
        });
    }

    /**
     * Откатить миграцию
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};
