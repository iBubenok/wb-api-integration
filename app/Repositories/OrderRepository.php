<?php

namespace App\Repositories;

use App\Models\Order;

/**
 * Репозиторий заказов
 *
 * @author Yan Bubenok <yan@bubenok.com>
 */
class OrderRepository extends BaseRepository
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }
}
