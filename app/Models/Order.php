<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Модель заказа
 *
 * @property int $id
 * @property string $external_id
 * @property string $order_date
 * @property string|null $order_datetime
 * @property string $order_number
 * @property string|null $article
 * @property string|null $barcode
 * @property float $price
 * @property float $discount
 * @property int $quantity
 * @property string|null $status
 * @property string|null $warehouse
 * @property string|null $region
 * @property string|null $customer_name
 * @property string|null $customer_phone
 * @property array|null $additional_data
 *
 * @author Yan Bubenok <yan@bubenok.com>
 */
class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Таблица модели
     *
     * @var string
     */
    protected $table = 'orders';

    /**
     * Массово назначаемые атрибуты
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id',
        'order_date',
        'order_datetime',
        'order_number',
        'article',
        'barcode',
        'price',
        'discount',
        'quantity',
        'status',
        'warehouse',
        'region',
        'customer_name',
        'customer_phone',
        'additional_data',
    ];

    /**
     * Атрибуты, которые должны быть приведены к типам
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order_date' => 'date',
        'order_datetime' => 'datetime',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'quantity' => 'integer',
        'additional_data' => 'array',
        'deleted_at' => 'datetime',
    ];

    /**
     * Получить заказ по внешнему ID
     */
    public static function findByExternalId(string $externalId): ?self
    {
        return static::where('external_id', $externalId)->first();
    }

    /**
     * Получить заказ по номеру заказа
     */
    public static function findByOrderNumber(string $orderNumber): ?self
    {
        return static::where('order_number', $orderNumber)->first();
    }

    /**
     * Проверить существование заказа по внешнему ID
     */
    public static function existsByExternalId(string $externalId): bool
    {
        return static::where('external_id', $externalId)->exists();
    }
}
