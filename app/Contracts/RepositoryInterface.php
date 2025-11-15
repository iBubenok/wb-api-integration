<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Базовый интерфейс репозитория
 *
 * Определяет общие методы для работы с данными
 *
 * @author Yan Bubenok <yan@bubenok.com>
 */
interface RepositoryInterface
{
    /**
     * Получить все записи
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Найти запись по ID
     *
     * @param int $id
     * @return Model|null
     */
    public function find(int $id): ?Model;

    /**
     * Создать новую запись
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Обновить существующую запись
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Удалить запись
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Создать или обновить запись по внешнему ID
     *
     * @param string $externalId
     * @param array $data
     * @return Model
     */
    public function updateOrCreateByExternalId(string $externalId, array $data): Model;

    /**
     * Получить запись по внешнему ID
     *
     * @param string $externalId
     * @return Model|null
     */
    public function findByExternalId(string $externalId): ?Model;

    /**
     * Проверить существование записи по внешнему ID
     *
     * @param string $externalId
     * @return bool
     */
    public function existsByExternalId(string $externalId): bool;
}
