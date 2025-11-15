<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Модель поступления
 *
 * @property int $id
 * @property string $external_id
 * @property string $income_date
 * @property string|null $income_datetime
 * @property string $article
 * @property string|null $barcode
 * @property int $quantity
 * @property float $price
 * @property string|null $warehouse
 * @property string|null $supply_number
 * @property string|null $status
 * @property array|null $additional_data
 *
 * @author Yan Bubenok <yan@bubenok.com>
 */
class Income extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Таблица модели
     *
     * @var string
     */
    protected $table = 'incomes';

    /**
     * Массово назначаемые атрибуты
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id',
        'income_date',
        'income_datetime',
        'article',
        'barcode',
        'quantity',
        'price',
        'warehouse',
        'supply_number',
        'status',
        'additional_data',
    ];

    /**
     * Атрибуты, которые должны быть приведены к типам
     *
     * @var array<string, string>
     */
    protected $casts = [
        'income_date' => 'date',
        'income_datetime' => 'datetime',
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'additional_data' => 'array',
        'deleted_at' => 'datetime',
    ];

    /**
     * Получить поступление по внешнему ID
     */
    public static function findByExternalId(string $externalId): ?self
    {
        return static::where('external_id', $externalId)->first();
    }

    /**
     * Получить поступления по номеру поставки
     */
    public static function findBySupplyNumber(string $supplyNumber)
    {
        return static::where('supply_number', $supplyNumber)->get();
    }

    /**
     * Проверить существование поступления по внешнему ID
     */
    public static function existsByExternalId(string $externalId): bool
    {
        return static::where('external_id', $externalId)->exists();
    }
}
