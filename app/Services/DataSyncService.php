<?php

namespace App\Services;

use App\Models\SyncLog;
use App\Repositories\SaleRepository;
use App\Repositories\OrderRepository;
use App\Repositories\StockRepository;
use App\Repositories\IncomeRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Сервис синхронизации данных
 *
 * Обеспечивает сохранение данных из API в базу данных
 * с логированием процесса и обработкой ошибок
 *
 * @author Yan Bubenok <yan@bubenok.com>
 */
class DataSyncService
{
    private DataFetcherService $dataFetcher;
    private SaleRepository $saleRepository;
    private OrderRepository $orderRepository;
    private StockRepository $stockRepository;
    private IncomeRepository $incomeRepository;

    public function __construct(
        DataFetcherService $dataFetcher,
        SaleRepository $saleRepository,
        OrderRepository $orderRepository,
        StockRepository $stockRepository,
        IncomeRepository $incomeRepository
    ) {
        $this->dataFetcher = $dataFetcher;
        $this->saleRepository = $saleRepository;
        $this->orderRepository = $orderRepository;
        $this->stockRepository = $stockRepository;
        $this->incomeRepository = $incomeRepository;
    }

    /**
     * Синхронизировать продажи
     */
    public function syncSales(Carbon $dateFrom, Carbon $dateTo): SyncLog
    {
        $syncLog = SyncLog::startSync(SyncLog::ENTITY_SALES, [
            'date_from' => $dateFrom->toDateString(),
            'date_to' => $dateTo->toDateString(),
        ]);

        try {
            $sales = $this->dataFetcher->fetchSales($dateFrom, $dateTo);

            foreach ($sales as $saleData) {
                try {
                    $this->saveSale($saleData);
                    $syncLog->incrementProcessed();

                    if ($this->saleRepository->existsByExternalId($saleData['id'] ?? '')) {
                        $syncLog->incrementUpdated();
                    } else {
                        $syncLog->incrementCreated();
                    }
                } catch (\Exception $e) {
                    $syncLog->incrementFailed();
                    Log::error('Failed to save sale', [
                        'data' => $saleData,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $syncLog->complete();
        } catch (\Exception $e) {
            $syncLog->fail(['message' => $e->getMessage()]);
            Log::error('Sales sync failed', ['error' => $e->getMessage()]);
        }

        return $syncLog;
    }

    /**
     * Синхронизировать заказы
     */
    public function syncOrders(Carbon $dateFrom, Carbon $dateTo): SyncLog
    {
        $syncLog = SyncLog::startSync(SyncLog::ENTITY_ORDERS, [
            'date_from' => $dateFrom->toDateString(),
            'date_to' => $dateTo->toDateString(),
        ]);

        try {
            $orders = $this->dataFetcher->fetchOrders($dateFrom, $dateTo);

            foreach ($orders as $orderData) {
                try {
                    $existed = $this->orderRepository->existsByExternalId($orderData['id'] ?? '');
                    $this->saveOrder($orderData);
                    $syncLog->incrementProcessed();

                    if ($existed) {
                        $syncLog->incrementUpdated();
                    } else {
                        $syncLog->incrementCreated();
                    }
                } catch (\Exception $e) {
                    $syncLog->incrementFailed();
                    Log::error('Failed to save order', [
                        'data' => $orderData,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $syncLog->complete();
        } catch (\Exception $e) {
            $syncLog->fail(['message' => $e->getMessage()]);
            Log::error('Orders sync failed', ['error' => $e->getMessage()]);
        }

        return $syncLog;
    }

    /**
     * Синхронизировать остатки
     */
    public function syncStocks(Carbon $date): SyncLog
    {
        $syncLog = SyncLog::startSync(SyncLog::ENTITY_STOCKS, [
            'date' => $date->toDateString(),
        ]);

        try {
            $stocks = $this->dataFetcher->fetchStocks($date);

            foreach ($stocks as $stockData) {
                try {
                    $existed = $this->stockRepository->existsByExternalId($stockData['id'] ?? '');
                    $this->saveStock($stockData, $date);
                    $syncLog->incrementProcessed();

                    if ($existed) {
                        $syncLog->incrementUpdated();
                    } else {
                        $syncLog->incrementCreated();
                    }
                } catch (\Exception $e) {
                    $syncLog->incrementFailed();
                    Log::error('Failed to save stock', [
                        'data' => $stockData,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $syncLog->complete();
        } catch (\Exception $e) {
            $syncLog->fail(['message' => $e->getMessage()]);
            Log::error('Stocks sync failed', ['error' => $e->getMessage()]);
        }

        return $syncLog;
    }

    /**
     * Синхронизировать поступления
     */
    public function syncIncomes(Carbon $dateFrom, Carbon $dateTo): SyncLog
    {
        $syncLog = SyncLog::startSync(SyncLog::ENTITY_INCOMES, [
            'date_from' => $dateFrom->toDateString(),
            'date_to' => $dateTo->toDateString(),
        ]);

        try {
            $incomes = $this->dataFetcher->fetchIncomes($dateFrom, $dateTo);

            foreach ($incomes as $incomeData) {
                try {
                    $existed = $this->incomeRepository->existsByExternalId($incomeData['id'] ?? '');
                    $this->saveIncome($incomeData);
                    $syncLog->incrementProcessed();

                    if ($existed) {
                        $syncLog->incrementUpdated();
                    } else {
                        $syncLog->incrementCreated();
                    }
                } catch (\Exception $e) {
                    $syncLog->incrementFailed();
                    Log::error('Failed to save income', [
                        'data' => $incomeData,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $syncLog->complete();
        } catch (\Exception $e) {
            $syncLog->fail(['message' => $e->getMessage()]);
            Log::error('Incomes sync failed', ['error' => $e->getMessage()]);
        }

        return $syncLog;
    }

    /**
     * Синхронизировать все данные
     */
    public function syncAll(Carbon $dateFrom, Carbon $dateTo): array
    {
        Log::info('Starting full sync', [
            'date_from' => $dateFrom->toDateString(),
            'date_to' => $dateTo->toDateString(),
        ]);

        return [
            'sales' => $this->syncSales($dateFrom, $dateTo),
            'orders' => $this->syncOrders($dateFrom, $dateTo),
            'stocks' => $this->syncStocks($dateTo),
            'incomes' => $this->syncIncomes($dateFrom, $dateTo),
        ];
    }

    /**
     * Сохранить продажу
     */
    private function saveSale(array $data): void
    {
        $this->saleRepository->updateOrCreateByExternalId(
            $data['id'] ?? uniqid('sale_'),
            [
                'external_id' => $data['id'] ?? uniqid('sale_'),
                'sale_date' => $data['date'] ?? now()->toDateString(),
                'sale_datetime' => $data['datetime'] ?? null,
                'order_number' => $data['orderNumber'] ?? null,
                'article' => $data['article'] ?? null,
                'barcode' => $data['barcode'] ?? null,
                'price' => $data['price'] ?? 0,
                'discount' => $data['discount'] ?? 0,
                'final_price' => $data['finalPrice'] ?? 0,
                'quantity' => $data['quantity'] ?? 1,
                'warehouse' => $data['warehouse'] ?? null,
                'region' => $data['region'] ?? null,
                'additional_data' => $data,
            ]
        );
    }

    /**
     * Сохранить заказ
     */
    private function saveOrder(array $data): void
    {
        $this->orderRepository->updateOrCreateByExternalId(
            $data['id'] ?? uniqid('order_'),
            [
                'external_id' => $data['id'] ?? uniqid('order_'),
                'order_date' => $data['date'] ?? now()->toDateString(),
                'order_datetime' => $data['datetime'] ?? null,
                'order_number' => $data['orderNumber'] ?? uniqid('order_'),
                'article' => $data['article'] ?? null,
                'barcode' => $data['barcode'] ?? null,
                'price' => $data['price'] ?? 0,
                'discount' => $data['discount'] ?? 0,
                'quantity' => $data['quantity'] ?? 1,
                'status' => $data['status'] ?? null,
                'warehouse' => $data['warehouse'] ?? null,
                'region' => $data['region'] ?? null,
                'customer_name' => $data['customerName'] ?? null,
                'customer_phone' => $data['customerPhone'] ?? null,
                'additional_data' => $data,
            ]
        );
    }

    /**
     * Сохранить остаток
     */
    private function saveStock(array $data, Carbon $date): void
    {
        $this->stockRepository->updateOrCreateByExternalId(
            $data['id'] ?? uniqid('stock_'),
            [
                'external_id' => $data['id'] ?? uniqid('stock_'),
                'stock_date' => $date->toDateString(),
                'article' => $data['article'] ?? '',
                'barcode' => $data['barcode'] ?? null,
                'warehouse' => $data['warehouse'] ?? null,
                'quantity' => $data['quantity'] ?? 0,
                'quantity_full' => $data['quantityFull'] ?? 0,
                'category' => $data['category'] ?? null,
                'subject' => $data['subject'] ?? null,
                'brand' => $data['brand'] ?? null,
                'additional_data' => $data,
            ]
        );
    }

    /**
     * Сохранить поступление
     */
    private function saveIncome(array $data): void
    {
        $this->incomeRepository->updateOrCreateByExternalId(
            $data['id'] ?? uniqid('income_'),
            [
                'external_id' => $data['id'] ?? uniqid('income_'),
                'income_date' => $data['date'] ?? now()->toDateString(),
                'income_datetime' => $data['datetime'] ?? null,
                'article' => $data['article'] ?? '',
                'barcode' => $data['barcode'] ?? null,
                'quantity' => $data['quantity'] ?? 0,
                'price' => $data['price'] ?? 0,
                'warehouse' => $data['warehouse'] ?? null,
                'supply_number' => $data['supplyNumber'] ?? null,
                'status' => $data['status'] ?? null,
                'additional_data' => $data,
            ]
        );
    }
}
