<?php

namespace App\Repositories;

use App\Models\Sale;

/**
 * Репозиторий продаж
 *
 * @author Yan Bubenok <yan@bubenok.com>
 */
class SaleRepository extends BaseRepository
{
    public function __construct(Sale $model)
    {
        parent::__construct($model);
    }
}
