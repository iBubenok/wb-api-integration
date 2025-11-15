<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\RequestException;

/**
 * Сервис для работы с WB API
 *
 * Обеспечивает низкоуровневое взаимодействие с API,
 * включая аутентификацию, обработку ошибок и логирование
 *
 * @author Yan Bubenok <yan@bubenok.com>
 */
class ApiClientService
{
    /**
     * Базовый URL API
     */
    private string $baseUrl;

    /**
     * API ключ для аутентификации
     */
    private string $apiKey;

    /**
     * Таймаут запроса в секундах
     */
    private int $timeout;

    /**
     * Конструктор сервиса
     */
    public function __construct()
    {
        $this->baseUrl = 'http://' . config('services.wb_api.host');
        $this->apiKey = config('services.wb_api.key');
        $this->timeout = config('services.wb_api.timeout', 30);
    }

    /**
     * Выполнить GET запрос к API
     *
     * @param string $endpoint Эндпоинт API
     * @param array $params Параметры запроса
     * @return array Данные ответа
     * @throws \Exception
     */
    public function get(string $endpoint, array $params = []): array
    {
        try {
            // Добавляем API ключ к параметрам
            $params['key'] = $this->apiKey;

            $url = $this->baseUrl . $endpoint;

            Log::info('WB API Request', [
                'url' => $url,
                'params' => array_merge($params, ['key' => '***']), // Скрываем ключ в логах
            ]);

            $response = Http::timeout($this->timeout)
                ->get($url, $params);

            if (!$response->successful()) {
                Log::error('WB API Error Response', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \Exception(
                    'API request failed with status ' . $response->status(),
                    $response->status()
                );
            }

            $data = $response->json();

            Log::info('WB API Response', [
                'endpoint' => $endpoint,
                'records_count' => is_array($data['data'] ?? null) ? count($data['data']) : 0,
            ]);

            return $data;

        } catch (RequestException $e) {
            Log::error('WB API Request Exception', [
                'endpoint' => $endpoint,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception('API request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Получить данные с пагинацией
     *
     * @param string $endpoint Эндпоинт API
     * @param array $params Параметры запроса
     * @param int $limit Количество записей на страницу
     * @return \Generator Генератор данных по страницам
     */
    public function getPaginated(string $endpoint, array $params = [], int $limit = 500): \Generator
    {
        $page = 1;
        $params['limit'] = $limit;

        do {
            $params['page'] = $page;

            $response = $this->get($endpoint, $params);
            $data = $response['data'] ?? [];

            if (!empty($data)) {
                yield $data;
                $page++;
            }

            // Если получили меньше записей чем лимит, значит это последняя страница
            $hasMore = count($data) === $limit;

        } while ($hasMore && !empty($data));
    }

    /**
     * Получить все данные со всех страниц
     *
     * @param string $endpoint Эндпоинт API
     * @param array $params Параметры запроса
     * @param int $limit Количество записей на страницу
     * @return array Все данные
     */
    public function getAll(string $endpoint, array $params = [], int $limit = 500): array
    {
        $allData = [];

        foreach ($this->getPaginated($endpoint, $params, $limit) as $pageData) {
            $allData = array_merge($allData, $pageData);
        }

        return $allData;
    }

    /**
     * Проверить доступность API
     *
     * @return bool
     */
    public function checkConnection(): bool
    {
        try {
            $response = Http::timeout(5)->get($this->baseUrl . '/api/health');
            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('WB API connection check failed', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
