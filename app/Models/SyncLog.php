<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Модель лога синхронизации
 *
 * @property int $id
 * @property string $entity_type
 * @property string $started_at
 * @property string|null $finished_at
 * @property string $status
 * @property int $records_processed
 * @property int $records_created
 * @property int $records_updated
 * @property int $records_failed
 * @property array|null $error_details
 * @property array|null $metadata
 *
 * @author Yan Bubenok <yan@bubenok.com>
 */
class SyncLog extends Model
{
    use HasFactory;

    /**
     * Таблица модели
     *
     * @var string
     */
    protected $table = 'sync_logs';

    /**
     * Константы статусов
     */
    public const STATUS_RUNNING = 'running';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    /**
     * Константы типов сущностей
     */
    public const ENTITY_SALES = 'sales';
    public const ENTITY_ORDERS = 'orders';
    public const ENTITY_STOCKS = 'stocks';
    public const ENTITY_INCOMES = 'incomes';

    /**
     * Массово назначаемые атрибуты
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entity_type',
        'started_at',
        'finished_at',
        'status',
        'records_processed',
        'records_created',
        'records_updated',
        'records_failed',
        'error_details',
        'metadata',
    ];

    /**
     * Атрибуты, которые должны быть приведены к типам
     *
     * @var array<string, string>
     */
    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'records_processed' => 'integer',
        'records_created' => 'integer',
        'records_updated' => 'integer',
        'records_failed' => 'integer',
        'error_details' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Начать новую синхронизацию
     */
    public static function startSync(string $entityType, array $metadata = []): self
    {
        return static::create([
            'entity_type' => $entityType,
            'started_at' => now(),
            'status' => self::STATUS_RUNNING,
            'records_processed' => 0,
            'records_created' => 0,
            'records_updated' => 0,
            'records_failed' => 0,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Завершить синхронизацию успешно
     */
    public function complete(): void
    {
        $this->update([
            'finished_at' => now(),
            'status' => self::STATUS_COMPLETED,
        ]);
    }

    /**
     * Завершить синхронизацию с ошибкой
     */
    public function fail(array $errorDetails = []): void
    {
        $this->update([
            'finished_at' => now(),
            'status' => self::STATUS_FAILED,
            'error_details' => $errorDetails,
        ]);
    }

    /**
     * Увеличить счетчик обработанных записей
     */
    public function incrementProcessed(int $count = 1): void
    {
        $this->increment('records_processed', $count);
    }

    /**
     * Увеличить счетчик созданных записей
     */
    public function incrementCreated(int $count = 1): void
    {
        $this->increment('records_created', $count);
    }

    /**
     * Увеличить счетчик обновленных записей
     */
    public function incrementUpdated(int $count = 1): void
    {
        $this->increment('records_updated', $count);
    }

    /**
     * Увеличить счетчик неудачных записей
     */
    public function incrementFailed(int $count = 1): void
    {
        $this->increment('records_failed', $count);
    }
}
