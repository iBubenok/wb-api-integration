<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Модель складского остатка
 *
 * @property int $id
 * @property string $external_id
 * @property string $stock_date
 * @property string $article
 * @property string|null $barcode
 * @property string|null $warehouse
 * @property int $quantity
 * @property int $quantity_full
 * @property string|null $category
 * @property string|null $subject
 * @property string|null $brand
 * @property array|null $additional_data
 *
 * @author Yan Bubenok <yan@bubenok.com>
 */
class Stock extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Таблица модели
     *
     * @var string
     */
    protected $table = 'stocks';

    /**
     * Массово назначаемые атрибуты
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id',
        'stock_date',
        'article',
        'barcode',
        'warehouse',
        'quantity',
        'quantity_full',
        'category',
        'subject',
        'brand',
        'additional_data',
    ];

    /**
     * Атрибуты, которые должны быть приведены к типам
     *
     * @var array<string, string>
     */
    protected $casts = [
        'stock_date' => 'date',
        'quantity' => 'integer',
        'quantity_full' => 'integer',
        'additional_data' => 'array',
        'deleted_at' => 'datetime',
    ];

    /**
     * Получить остаток по внешнему ID
     */
    public static function findByExternalId(string $externalId): ?self
    {
        return static::where('external_id', $externalId)->first();
    }

    /**
     * Получить остатки по артикулу
     */
    public static function findByArticle(string $article)
    {
        return static::where('article', $article)->get();
    }

    /**
     * Проверить существование остатка по внешнему ID
     */
    public static function existsByExternalId(string $externalId): bool
    {
        return static::where('external_id', $externalId)->exists();
    }
}
