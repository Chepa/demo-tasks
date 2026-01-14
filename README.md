## API для управления задачами

### 1. Настройка переменных окружения

```bash
cp .env.example .env
```

### 2. Запуск Docker-контейнеров

```bash
docker compose up -d
```

### 3. Установка зависимостей Laravel

```bash
# Установка PHP зависимостей (если вы не устанавливали Laravel а он уже был в репозитории)
docker exec app composer install

# Сгенерировать ключ приложения
docker exec app php artisan key:generate

# Применение миграций
docker exec app php artisan migrate
```

### 4. Установка зависимостей npm зависимостей

```bash
# установка зависимостей
docker exec app npm i

# сборка проекта
docker exec app npm run build
```

## Доступ к приложению
- **Веб-интерфейс**: http://localhost

## Тестирование
docker exec app php artisan test

## OpenAPI документация
http://localhost/swagger
