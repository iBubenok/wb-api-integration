<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Модель продажи
 *
 * @property int $id
 * @property string $external_id
 * @property string $sale_date
 * @property string|null $sale_datetime
 * @property string|null $order_number
 * @property string|null $article
 * @property string|null $barcode
 * @property float $price
 * @property float $discount
 * @property float $final_price
 * @property int $quantity
 * @property string|null $warehouse
 * @property string|null $region
 * @property array|null $additional_data
 *
 * @author Yan Bubenok <yan@bubenok.com>
 */
class Sale extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Таблица модели
     *
     * @var string
     */
    protected $table = 'sales';

    /**
     * Массово назначаемые атрибуты
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id',
        'sale_date',
        'sale_datetime',
        'order_number',
        'article',
        'barcode',
        'price',
        'discount',
        'final_price',
        'quantity',
        'warehouse',
        'region',
        'additional_data',
    ];

    /**
     * Атрибуты, которые должны быть приведены к типам
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sale_date' => 'date',
        'sale_datetime' => 'datetime',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'final_price' => 'decimal:2',
        'quantity' => 'integer',
        'additional_data' => 'array',
        'deleted_at' => 'datetime',
    ];

    /**
     * Получить продажу по внешнему ID
     */
    public static function findByExternalId(string $externalId): ?self
    {
        return static::where('external_id', $externalId)->first();
    }

    /**
     * Проверить существование продажи по внешнему ID
     */
    public static function existsByExternalId(string $externalId): bool
    {
        return static::where('external_id', $externalId)->exists();
    }
}
