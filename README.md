# WB API Integration

Приложение для интеграции с WB API и сохранения данных в MySQL базу данных.

**Автор:** Yan Bubenok
**Email:** yan@bubenok.com
**Telegram:** @iBubenok

## Описание проекта

Данное приложение представляет собой Laravel-решение для автоматизации получения и хранения данных из WB API. Поддерживаются следующие типы данных:

- **Продажи (Sales)** - информация о совершенных продажах
- **Заказы (Orders)** - данные о заказах клиентов
- **Остатки (Stocks)** - информация об остатках товаров на складах
- **Поступления (Incomes)** - данные о поступлении товаров

### Ключевые особенности

- Архитектура соответствует принципам SOLID
- Реализован Repository Pattern для работы с данными
- Service Layer для бизнес-логики
- Полное логирование операций синхронизации
- Docker-окружение для быстрого развертывания
- Кэширование через Redis
- Обработка ошибок и восстановление после сбоев

## Требования

- PHP 8.2+
- Composer 2.x
- Docker & Docker Compose v2
- MySQL 8.0
- Redis

## Установка

### 1. Клонирование репозитория

```bash
git clone <repository-url>
cd wb-api-integration
```

### 2. Настройка окружения

Скопируйте файл с примером окружения:

```bash
cp .env.example .env
```

Отредактируйте `.env` файл и установите необходимые параметры. Основные настройки:

```env
# Настройки приложения
APP_NAME="WB API Integration"
APP_URL=http://localhost:8000

# Настройки базы данных
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=wb_integration
DB_USERNAME=wb_user
DB_PASSWORD=secure_password_123

# Настройки Redis
CACHE_STORE=redis
REDIS_HOST=redis
REDIS_PORT=6379

# Настройки WB API
WB_API_HOST=109.73.206.144:6969
WB_API_KEY=E6kUTYrYwZq2tN4QEtyzsbEBk3ie
WB_API_TIMEOUT=30
```

### 3. Запуск Docker контейнеров

```bash
docker compose up -d
```

Это запустит следующие сервисы:
- **app** - PHP-FPM контейнер с приложением
- **nginx** - Веб-сервер
- **mysql** - База данных MySQL 8.0
- **redis** - Кэш-сервер Redis

### 4. Установка зависимостей

```bash
docker compose exec app composer install
```

### 5. Генерация ключа приложения

```bash
docker compose exec app php artisan key:generate
```

### 6. Выполнение миграций

```bash
docker compose exec app php artisan migrate
```

## Использование

### Синхронизация данных

Приложение предоставляет консольную команду `data:sync` для синхронизации данных из API.

#### Синхронизация всех типов данных

```bash
docker compose exec app php artisan data:sync
```

По умолчанию синхронизируются данные за последние 7 дней.

#### Синхронизация конкретного типа данных

```bash
# Только продажи
docker compose exec app php artisan data:sync sales

# Только заказы
docker compose exec app php artisan data:sync orders

# Только остатки
docker compose exec app php artisan data:sync stocks

# Только поступления
docker compose exec app php artisan data:sync incomes
```

#### Указание периода синхронизации

```bash
# Синхронизация за указанный период
docker compose exec app php artisan data:sync all --from=2024-01-01 --to=2024-01-31

# Синхронизация за последние 30 дней
docker compose exec app php artisan data:sync all --days=30
```

### Примеры использования

```bash
# Получить продажи за последние 14 дней
docker compose exec app php artisan data:sync sales --days=14

# Получить заказы за январь 2024
docker compose exec app php artisan data:sync orders --from=2024-01-01 --to=2024-01-31

# Получить текущие остатки
docker compose exec app php artisan data:sync stocks

# Синхронизировать все данные за последний месяц
docker compose exec app php artisan data:sync all --days=30
```

## Доступы к базе данных

### Production окружение (Удаленная БД)

**Хостинг:** Aiven MySQL 8.0 (30 дней бесплатно)

✅ **База данных развернута и готова к работе!**

**Доступы к production БД:**

- **Host:** wb-api-integration-bubenok-536b.c.aivencloud.com
- **Port:** 19555
- **Database:** wb_integration
- **Username:** avnadmin
- **Password:** [предоставляется по запросу: yan@bubenok.com]
- **SSL:** Включено автоматически

**Состояние:**
- ✅ База данных создана
- ✅ Все 14 таблиц успешно созданы
- ✅ Миграции применены
- ✅ Подключение протестировано
- ✅ Тестовая синхронизация выполнена
- ✅ Полная синхронизация за 7 дней выполнена

**Результаты синхронизации:**

1. **Тестовая синхронизация** (1 день - 2025-11-14):
   - Период: 1 день
   - Обработано: 457 записей о продажах
   - Создано: 457 записей
   - Ошибок: 0
   - Время: 20 минут
   - Статус: ✅ Завершена успешно

2. **Полная синхронизация** (7 дней - 2025-11-08 по 2025-11-15):
   - Период: 7 дней
   - Начало: 2025-11-15 16:07:23
   - Окончание: 2025-11-16 01:04:58
   - Общее время: ~9 часов
   - Статус: ✅ **Завершена полностью**

   **Детальная статистика по типам данных:**

   | Тип данных | Обработано | Создано | Обновлено | Ошибок |
   |------------|------------|---------|-----------|--------|
   | **Sales (Продажи)** | 3,236 | 3,236 | 0 | 0 |
   | **Orders (Заказы)** | 3,342 | 3,342 | 0 | 0 |
   | **Stocks (Остатки)** | 3,038 | 3,038 | 0 | 0 |
   | **Incomes (Поступления)** | 56 | 56 | 0 | 0 |
   | **ИТОГО** | **9,672** | **9,672** | **0** | **0** |

**Текущие данные в production БД (Aiven):**
- **Sales (Продажи)**: 6,566 записей
- **Orders (Заказы)**: 6,684 записей
- **Stocks (Остатки)**: 6,076 записей
- **Incomes (Поступления)**: 112 записей
- **ВСЕГО**: **19,438 записей**

> **Успешный результат:** Все 4 типа данных успешно синхронизированы за 7 дней без единой ошибки.
> База данных содержит полный набор данных и готова к использованию.

**Список таблиц в БД:**
- `sales` - продажи (19 полей)
- `orders` - заказы (20 полей)
- `stocks` - остатки на складах (16 полей)
- `incomes` - поступления товаров (16 полей)
- `sync_logs` - логи синхронизации (13 полей)
- `users` - пользователи системы (стандартная таблица Laravel)
- `cache` - кэш (стандартная таблица Laravel)
- `cache_locks` - блокировки кэша (стандартная таблица Laravel)
- `jobs` - очередь задач (стандартная таблица Laravel)
- `job_batches` - пакеты задач (стандартная таблица Laravel)
- `failed_jobs` - неудачные задачи (стандартная таблица Laravel)
- `migrations` - история миграций (стандартная таблица Laravel)

### Локальное окружение (Docker)

- **Host:** localhost (или 127.0.0.1)
- **Port:** 3306
- **Database:** wb_integration
- **Username:** wb_user
- **Password:** secure_password_123
- **Root Password:** root_password_123

### Подключение через MySQL клиент

```bash
# Production БД
mysql -h [production-host] -P 3306 -u [username] -p
# Пароль будет предоставлен отдельно

# Локальная БД
mysql -h 127.0.0.1 -P 3306 -u wb_user -p
# Пароль: secure_password_123
```

### Подключение через Docker (локально)

```bash
docker compose exec mysql mysql -u wb_user -p wb_integration
# Пароль: secure_password_123
```

## Структура таблиц

### sales (Продажи)

Хранит информацию о совершенных продажах.

| Колонка | Тип | Описание |
|---------|-----|----------|
| id | BIGINT | Первичный ключ |
| external_id | VARCHAR | Уникальный ID из API |
| sale_date | DATE | Дата продажи |
| order_number | VARCHAR | Номер заказа |
| article | VARCHAR | Артикул товара |
| price | DECIMAL | Цена |
| discount | DECIMAL | Скидка |
| final_price | DECIMAL | Итоговая цена |
| quantity | INT | Количество |
| warehouse | VARCHAR | Склад |
| region | VARCHAR | Регион |

### orders (Заказы)

Содержит данные о заказах клиентов.

| Колонка | Тип | Описание |
|---------|-----|----------|
| id | BIGINT | Первичный ключ |
| external_id | VARCHAR | Уникальный ID из API |
| order_date | DATE | Дата заказа |
| order_number | VARCHAR | Номер заказа |
| article | VARCHAR | Артикул товара |
| price | DECIMAL | Цена |
| status | VARCHAR | Статус заказа |
| warehouse | VARCHAR | Склад |

### stocks (Остатки)

Информация об остатках товаров на складах.

| Колонка | Тип | Описание |
|---------|-----|----------|
| id | BIGINT | Первичный ключ |
| external_id | VARCHAR | Уникальный ID из API |
| stock_date | DATE | Дата данных об остатках |
| article | VARCHAR | Артикул товара |
| warehouse | VARCHAR | Склад |
| quantity | INT | Количество на складе |
| category | VARCHAR | Категория |
| brand | VARCHAR | Бренд |

### incomes (Поступления)

Данные о поступлении товаров.

| Колонка | Тип | Описание |
|---------|-----|----------|
| id | BIGINT | Первичный ключ |
| external_id | VARCHAR | Уникальный ID из API |
| income_date | DATE | Дата поступления |
| article | VARCHAR | Артикул товара |
| quantity | INT | Количество |
| price | DECIMAL | Цена |
| warehouse | VARCHAR | Склад |
| supply_number | VARCHAR | Номер поставки |

### sync_logs (Логи синхронизации)

Логи процесса синхронизации данных.

| Колонка | Тип | Описание |
|---------|-----|----------|
| id | BIGINT | Первичный ключ |
| entity_type | VARCHAR | Тип сущности |
| started_at | DATETIME | Время начала |
| finished_at | DATETIME | Время окончания |
| status | VARCHAR | Статус (running/completed/failed) |
| records_processed | INT | Обработано записей |
| records_created | INT | Создано записей |
| records_updated | INT | Обновлено записей |
| records_failed | INT | Ошибок |

## API эндпоинты

Приложение интегрируется со следующими эндпоинтами WB API:

- `GET /api/sales` - Получение продаж за период
- `GET /api/orders` - Получение заказов за период
- `GET /api/stocks` - Получение остатков на текущий день
- `GET /api/incomes` - Получение поступлений за период

Все эндпоинты поддерживают пагинацию (параметры `page` и `limit`).

## Тестирование

### Запуск всех тестов

```bash
docker compose exec app php artisan test
```

### Запуск конкретной группы тестов

```bash
# Unit тесты
docker compose exec app php artisan test --testsuite=Unit

# Feature тесты
docker compose exec app php artisan test --testsuite=Feature
```

### Запуск с покрытием кода

```bash
docker compose exec app php artisan test --coverage
```

## Логирование

Все операции логируются в файлы:

- **Основной лог:** `storage/logs/laravel.log`
- **Логи API:** Все запросы к WB API логируются с деталями
- **Логи синхронизации:** Сохраняются в таблицу `sync_logs`

Просмотр логов в реальном времени:

```bash
docker compose exec app tail -f storage/logs/laravel.log
```

## Архитектура

Подробное описание архитектурных решений и паттернов проектирования см. в файле [ARCHITECTURE.md](ARCHITECTURE.md).

## Устранение неполадок

### Проблемы с правами доступа

```bash
docker compose exec app chmod -R 775 storage bootstrap/cache
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Очистка кэша

```bash
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
```

### Пересоздание контейнеров

```bash
docker compose down
docker compose up -d --build
```

## Поддержка и контакты

При возникновении вопросов или проблем:

- **Email:** yan@bubenok.com
- **Telegram:** @iBubenok

## Лицензия

MIT License
