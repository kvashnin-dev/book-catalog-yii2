# Book Catalog Yii2

Минимальный стартовый проект для тестового задания: Yii2 + PHP 8 + MySQL/MariaDB в Docker Compose.

## Запуск

```bash
cp .env.example .env
docker compose up -d --build
```

Приложение будет доступно на http://localhost:8080.

## Команды

```bash
docker compose exec app php yii
docker compose exec app composer install
```

## Состав окружения

- `app` - PHP-FPM 8.3 с Composer и расширениями для Yii2/MySQL.
- `web` - Nginx, отдающий `web/index.php`.
- `db` - MySQL 8.

Директории `vendor`, `runtime` и `web/assets` не хранятся в репозитории.
