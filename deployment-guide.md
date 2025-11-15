# Инструкция по развертыванию БД на бесплатном хостинге

**Автор:** Yan Bubenok <yan@bubenok.com>

## Варианты бесплатных хостингов для MySQL

### Рекомендуемые варианты:

1. **Aiven (Лучший выбор)** - https://aiven.io
   - ✅ 30 дней бесплатно
   - ✅ MySQL 8.0
   - ✅ SSL соединение
   - ✅ Простая настройка

2. **Railway** - https://railway.app
   - ✅ $5 кредитов в месяц
   - ✅ MySQL 8.0
   - ✅ Автоматические бэкапы
   - ✅ Интеграция с GitHub

3. **PlanetScale (Альтернатива)** - https://planetscale.com
   - ✅ Бесплатный план навсегда
   - ✅ MySQL-совместимая БД
   - ✅ 5GB хранилища
   - ✅ 1 миллиард строк в месяц

4. **FreeSQLDatabase** - https://www.freesqldatabase.com
   - ✅ Бесплатно навсегда
   - ✅ MySQL 5.5
   - ⚠️ Ограниченный функционал

## Вариант 1: Развертывание на Aiven (Рекомендуется)

### Шаг 1: Регистрация

1. Перейдите на https://aiven.io
2. Нажмите "Sign Up"
3. Зарегистрируйтесь через GitHub или email
4. Подтвердите email

### Шаг 2: Создание MySQL сервиса

1. После входа нажмите "Create Service"
2. Выберите "MySQL"
3. Выберите "Free Plan" (30 дней)
4. Выберите ближайший регион (например, AWS EU-West-1)
5. Имя сервиса: `wb-api-integration`
6. Нажмите "Create Service"
7. Дождитесь запуска (2-3 минуты)

### Шаг 3: Получение доступов

После запуска сервиса вы увидите:

```
Host: mysql-xxxxx.aivencloud.com
Port: 12345
User: avnadmin
Password: [сгенерированный пароль]
Database: defaultdb
```

### Шаг 4: Создание БД и пользователя

1. В панели Aiven нажмите на ваш сервис
2. Перейдите в раздел "Databases"
3. Нажмите "Add Database"
4. Имя: `wb_integration`
5. Создайте базу

### Шаг 5: Применение миграций

```bash
# 1. Обновите .env с данными Aiven
DB_CONNECTION=mysql
DB_HOST=mysql-xxxxx.aivencloud.com
DB_PORT=12345
DB_DATABASE=wb_integration
DB_USERNAME=avnadmin
DB_PASSWORD=[ваш пароль из Aiven]

# 2. Примените миграции
php artisan migrate

# 3. Проверьте подключение
php artisan db:show
```

### Шаг 6: Обновление README.md

После успешного развертывания обновите README.md:

```markdown
### Production окружение (Удаленная БД)

**Хостинг:** Aiven MySQL 8.0

- **Host:** mysql-xxxxx.aivencloud.com
- **Port:** 12345
- **Database:** wb_integration
- **Username:** avnadmin
- **Password:** [предоставляется отдельно]

**SSL:** Обязательно (сертификаты предоставляются Aiven)
```

## Вариант 2: Развертывание на Railway

### Шаг 1: Регистрация и создание проекта

1. Перейдите на https://railway.app
2. Нажмите "Start a New Project"
3. Войдите через GitHub
4. Нажмите "New Project"
5. Выберите "Provision MySQL"

### Шаг 2: Получение доступов

1. Нажмите на созданный MySQL сервис
2. Перейдите в "Connect"
3. Скопируйте данные:

```
MYSQL_URL=mysql://root:password@containers-us-west-xxx.railway.app:1234/railway
```

Распарсите URL:
- Host: containers-us-west-xxx.railway.app
- Port: 1234
- User: root
- Password: password
- Database: railway

### Шаг 3: Создание отдельной БД (опционально)

```bash
# Подключитесь к Railway MySQL
mysql -h containers-us-west-xxx.railway.app -P 1234 -u root -p

# Создайте БД
CREATE DATABASE wb_integration;
USE wb_integration;
```

### Шаг 4: Применение миграций

```bash
# Обновите .env
DB_HOST=containers-us-west-xxx.railway.app
DB_PORT=1234
DB_DATABASE=wb_integration
DB_USERNAME=root
DB_PASSWORD=[пароль из Railway]

# Примените миграции
php artisan migrate
```

## Вариант 3: PlanetScale (MySQL-совместимый)

### Особенности PlanetScale:

⚠️ **Важно:** PlanetScale не поддерживает foreign keys, но это не критично для нашего проекта.

### Шаг 1: Создание базы

1. Перейдите на https://planetscale.com
2. Зарегистрируйтесь через GitHub
3. Нажмите "Create database"
4. Имя: `wb-api-integration`
5. Регион: выберите ближайший
6. План: "Hobby" (бесплатный)

### Шаг 2: Создание пароля

1. В базе перейдите в "Settings" → "Passwords"
2. Нажмите "New password"
3. Имя: `laravel-app`
4. Скопируйте данные:

```
Host: aws.connect.psdb.cloud
Username: xxxxxxxxxx
Password: pscale_pw_xxxxxxxxx
Database: wb-api-integration
SSL: Required
```

### Шаг 3: Настройка Laravel

```bash
# .env
DB_CONNECTION=mysql
DB_HOST=aws.connect.psdb.cloud
DB_PORT=3306
DB_DATABASE=wb-api-integration
DB_USERNAME=xxxxxxxxxx
DB_PASSWORD=pscale_pw_xxxxxxxxx
DB_SSLMODE=require

# Добавьте в config/database.php для mysql соединения:
'sslmode' => env('DB_SSLMODE', 'prefer'),
```

### Шаг 4: Миграции без foreign keys

Наши миграции уже совместимы с PlanetScale (не используют foreign keys напрямую).

```bash
php artisan migrate
```

## Вариант 4: FreeSQLDatabase (Самый простой)

### Шаг 1: Регистрация

1. Перейдите на https://www.freesqldatabase.com
2. Нажмите "Free MySQL Database"
3. Заполните форму:
   - Database Name: wb_integration
   - Username: [придумайте]
   - Password: [придумайте]
   - Server: выберите доступный

### Шаг 2: Получение доступов

После регистрации вы получите email с данными:

```
Server: sql12.freesqldatabase.com
Database Name: sql12_xxxxx
Username: sql12_xxxxx
Password: xxxxxxxxx
Port: 3306
```

### Шаг 3: Настройка и миграции

```bash
# .env
DB_HOST=sql12.freesqldatabase.com
DB_PORT=3306
DB_DATABASE=sql12_xxxxx
DB_USERNAME=sql12_xxxxx
DB_PASSWORD=xxxxxxxxx

# Миграции
php artisan migrate
```

⚠️ **Ограничения FreeSQLDatabase:**
- MySQL 5.5 (старая версия)
- Ограниченный размер БД
- Может быть медленным
- Не для production

## После развертывания

### 1. Обновите .env.example

```env
# Production Database (пример)
DB_CONNECTION=mysql
DB_HOST=your-production-host.com
DB_PORT=3306
DB_DATABASE=wb_integration
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 2. Обновите README.md

Замените секцию "Production окружение" реальными данными:

```markdown
### Production окружение (Удаленная БД)

**Хостинг:** [Aiven / Railway / PlanetScale / FreeSQLDatabase]

- **Host:** [реальный хост]
- **Port:** [реальный порт]
- **Database:** wb_integration
- **Username:** [реальный username]
- **Password:** [предоставляется отдельно для безопасности]

**Список таблиц в БД:**
- `sales` - продажи
- `orders` - заказы
- `stocks` - остатки
- `incomes` - поступления
- `sync_logs` - логи синхронизации
```

### 3. Запустите синхронизацию

```bash
# Синхронизируйте данные с production БД
php artisan data:sync all --days=7
```

### 4. Проверьте таблицы

```bash
# Подключитесь к БД
mysql -h [production-host] -u [username] -p

# Проверьте таблицы
SHOW TABLES;

# Проверьте данные
SELECT COUNT(*) FROM sales;
SELECT COUNT(*) FROM orders;
SELECT COUNT(*) FROM stocks;
SELECT COUNT(*) FROM incomes;
SELECT COUNT(*) FROM sync_logs;
```

## Безопасность

⚠️ **Важно:**

1. **Не коммитьте** реальные пароли в .env
2. **Не публикуйте** пароли в README.md
3. **Используйте** фразу "предоставляется отдельно"
4. **Передавайте** пароли работодателю по защищенному каналу

### Пример для README.md:

```markdown
- **Password:** [предоставляется по запросу: yan@bubenok.com]
```

## Рекомендации для работодателя

### Текст в README.md:

```markdown
## Доступ к Production БД

Для получения полных доступов к production базе данных обратитесь:
- **Email:** yan@bubenok.com
- **Telegram:** @iBubenok

Будут предоставлены:
- Полный URL подключения
- Username
- Password
- SSL сертификаты (если требуются)
```

## Автоматизация (опционально)

Можно создать команду для переключения между окружениями:

```bash
# Локальная БД
php artisan config:cache

# Production БД (после обновления .env)
php artisan config:cache
php artisan migrate --force
php artisan data:sync
```

## Проверочный чеклист

После развертывания проверьте:

- [ ] БД создана на хостинге
- [ ] Миграции применены успешно
- [ ] Все таблицы созданы (12 таблиц)
- [ ] Тестовая синхронизация прошла успешно
- [ ] Данные сохраняются в БД
- [ ] README.md обновлен с реальными доступами
- [ ] Пароль не опубликован в GitHub
- [ ] Подключение работает стабильно

## Поддержка

При возникновении проблем:
- Email: yan@bubenok.com
- Telegram: @iBubenok

---

**Рекомендация:** Используйте Aiven или Railway для лучшей производительности и надежности.
