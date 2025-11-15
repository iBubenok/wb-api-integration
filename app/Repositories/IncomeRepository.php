<?php

namespace App\Repositories;

use App\Models\Income;

/**
 * Репозиторий поступлений
 *
 * @author Yan Bubenok <yan@bubenok.com>
 */
class IncomeRepository extends BaseRepository
{
    public function __construct(Income $model)
    {
        parent::__construct($model);
    }
}
