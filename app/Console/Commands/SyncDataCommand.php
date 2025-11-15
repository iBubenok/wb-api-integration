<?php

namespace App\Console\Commands;

use App\Services\DataSyncService;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Команда для синхронизации данных из WB API
 *
 * @author Yan Bubenok <yan@bubenok.com>
 */
class SyncDataCommand extends Command
{
    protected $signature = 'data:sync
                            {type=all : Тип данных (all, sales, orders, stocks, incomes)}
                            {--from= : Дата начала (Y-m-d)}
                            {--to= : Дата окончания (Y-m-d)}
                            {--days=7 : Количество дней назад от сегодня}';

    protected $description = 'Синхронизация данных из WB API в базу данных';

    private DataSyncService $syncService;

    public function __construct(DataSyncService $syncService)
    {
        parent::__construct();
        $this->syncService = $syncService;
    }

    public function handle(): int
    {
        $type = $this->argument('type');

        $dateFrom = $this->option('from')
            ? Carbon::parse($this->option('from'))
            : Carbon::now()->subDays($this->option('days'));

        $dateTo = $this->option('to')
            ? Carbon::parse($this->option('to'))
            : Carbon::now();

        $this->info("Синхронизация {$type} с {$dateFrom->toDateString()} по {$dateTo->toDateString()}");

        $results = match($type) {
            'sales' => ['sales' => $this->syncService->syncSales($dateFrom, $dateTo)],
            'orders' => ['orders' => $this->syncService->syncOrders($dateFrom, $dateTo)],
            'stocks' => ['stocks' => $this->syncService->syncStocks($dateTo)],
            'incomes' => ['incomes' => $this->syncService->syncIncomes($dateFrom, $dateTo)],
            default => $this->syncService->syncAll($dateFrom, $dateTo),
        };

        $this->displayResults($results);

        return Command::SUCCESS;
    }

    private function displayResults(array $results): void
    {
        foreach ($results as $type => $log) {
            $this->line("\n" . strtoupper($type) . ":");
            $this->table(
                ['Метрика', 'Значение'],
                [
                    ['Статус', $log->status],
                    ['Обработано', $log->records_processed],
                    ['Создано', $log->records_created],
                    ['Обновлено', $log->records_updated],
                    ['Ошибок', $log->records_failed],
                ]
            );
        }
    }
}
