<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Сервис для получения данных из WB API
 *
 * Предоставляет высокоуровневые методы для получения данных
 * различных типов (продажи, заказы, остатки, поступления)
 *
 * @author Yan Bubenok <yan@bubenok.com>
 */
class DataFetcherService
{
    /**
     * API клиент
     */
    private ApiClientService $apiClient;

    /**
     * Конструктор сервиса
     */
    public function __construct(ApiClientService $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Получить продажи за период
     *
     * @param Carbon $dateFrom Дата начала
     * @param Carbon $dateTo Дата окончания
     * @return array Массив продаж
     */
    public function fetchSales(Carbon $dateFrom, Carbon $dateTo): array
    {
        Log::info('Fetching sales', [
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d'),
        ]);

        $params = [
            'dateFrom' => $dateFrom->format('Y-m-d'),
            'dateTo' => $dateTo->format('Y-m-d'),
        ];

        return $this->apiClient->getAll('/api/sales', $params);
    }

    /**
     * Получить заказы за период
     *
     * @param Carbon $dateFrom Дата начала
     * @param Carbon $dateTo Дата окончания
     * @return array Массив заказов
     */
    public function fetchOrders(Carbon $dateFrom, Carbon $dateTo): array
    {
        Log::info('Fetching orders', [
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d'),
        ]);

        $params = [
            'dateFrom' => $dateFrom->format('Y-m-d'),
            'dateTo' => $dateTo->format('Y-m-d'),
        ];

        return $this->apiClient->getAll('/api/orders', $params);
    }

    /**
     * Получить остатки на складах
     *
     * @param Carbon $date Дата для получения остатков
     * @return array Массив остатков
     */
    public function fetchStocks(Carbon $date): array
    {
        Log::info('Fetching stocks', [
            'date' => $date->format('Y-m-d'),
        ]);

        $params = [
            'dateFrom' => $date->format('Y-m-d'),
        ];

        return $this->apiClient->getAll('/api/stocks', $params);
    }

    /**
     * Получить поступления за период
     *
     * @param Carbon $dateFrom Дата начала
     * @param Carbon $dateTo Дата окончания
     * @return array Массив поступлений
     */
    public function fetchIncomes(Carbon $dateFrom, Carbon $dateTo): array
    {
        Log::info('Fetching incomes', [
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d'),
        ]);

        $params = [
            'dateFrom' => $dateFrom->format('Y-m-d'),
            'dateTo' => $dateTo->format('Y-m-d'),
        ];

        return $this->apiClient->getAll('/api/incomes', $params);
    }

    /**
     * Получить все данные за период
     *
     * @param Carbon $dateFrom Дата начала
     * @param Carbon $dateTo Дата окончания
     * @return array Ассоциативный массив со всеми типами данных
     */
    public function fetchAll(Carbon $dateFrom, Carbon $dateTo): array
    {
        Log::info('Fetching all data', [
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d'),
        ]);

        return [
            'sales' => $this->fetchSales($dateFrom, $dateTo),
            'orders' => $this->fetchOrders($dateFrom, $dateTo),
            'stocks' => $this->fetchStocks($dateTo), // Остатки на конечную дату
            'incomes' => $this->fetchIncomes($dateFrom, $dateTo),
        ];
    }

    /**
     * Получить данные с постраничной обработкой
     *
     * @param string $type Тип данных (sales, orders, stocks, incomes)
     * @param Carbon $dateFrom Дата начала
     * @param Carbon $dateTo Дата окончания
     * @param callable $callback Функция обработки каждой страницы
     * @return void
     */
    public function fetchPaginated(
        string $type,
        Carbon $dateFrom,
        Carbon $dateTo,
        callable $callback
    ): void {
        $endpoint = '/api/' . $type;
        $params = [
            'dateFrom' => $dateFrom->format('Y-m-d'),
        ];

        // Для всех типов кроме stocks нужен dateTo
        if ($type !== 'stocks') {
            $params['dateTo'] = $dateTo->format('Y-m-d');
        }

        foreach ($this->apiClient->getPaginated($endpoint, $params) as $pageData) {
            $callback($pageData);
        }
    }
}
