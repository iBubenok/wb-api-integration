<?php

namespace App\Repositories;

use App\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Базовый репозиторий
 *
 * Реализует общую логику работы с данными
 *
 * @author Yan Bubenok <yan@bubenok.com>
 */
abstract class BaseRepository implements RepositoryInterface
{
    /**
     * Модель для работы
     *
     * @var Model
     */
    protected Model $model;

    /**
     * Конструктор репозитория
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Получить все записи
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Найти запись по ID
     */
    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Создать новую запись
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Обновить существующую запись
     */
    public function update(int $id, array $data): bool
    {
        $record = $this->find($id);

        if (!$record) {
            return false;
        }

        return $record->update($data);
    }

    /**
     * Удалить запись
     */
    public function delete(int $id): bool
    {
        $record = $this->find($id);

        if (!$record) {
            return false;
        }

        return $record->delete();
    }

    /**
     * Создать или обновить запись по внешнему ID
     */
    public function updateOrCreateByExternalId(string $externalId, array $data): Model
    {
        return $this->model->updateOrCreate(
            ['external_id' => $externalId],
            $data
        );
    }

    /**
     * Получить запись по внешнему ID
     */
    public function findByExternalId(string $externalId): ?Model
    {
        return $this->model->where('external_id', $externalId)->first();
    }

    /**
     * Проверить существование записи по внешнему ID
     */
    public function existsByExternalId(string $externalId): bool
    {
        return $this->model->where('external_id', $externalId)->exists();
    }

    /**
     * Массовое создание записей
     *
     * @param array $records
     * @return int Количество созданных записей
     */
    public function bulkCreate(array $records): int
    {
        if (empty($records)) {
            return 0;
        }

        // Разбиваем на чанки для оптимизации
        $chunks = array_chunk($records, 500);
        $created = 0;

        foreach ($chunks as $chunk) {
            $this->model->insert($chunk);
            $created += count($chunk);
        }

        return $created;
    }

    /**
     * Получить модель
     */
    public function getModel(): Model
    {
        return $this->model;
    }
}
