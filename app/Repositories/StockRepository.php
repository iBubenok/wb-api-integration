<?php

namespace App\Repositories;

use App\Models\Stock;

/**
 * Репозиторий складских остатков
 *
 * @author Yan Bubenok <yan@bubenok.com>
 */
class StockRepository extends BaseRepository
{
    public function __construct(Stock $model)
    {
        parent::__construct($model);
    }
}
