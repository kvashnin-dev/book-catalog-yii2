# Каталог книг на Yii2

Web-приложение для тестового задания: каталог книг на Yii2, PHP 8.3 и MySQL/MariaDB.

## Запуск

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app php yii migrate --interactive=0
```

Приложение будет доступно на http://localhost:8080.

MinIO поднимается как S3-compatible хранилище:

- API: http://localhost:9000
- Console: http://localhost:9001
- логин/пароль по умолчанию: `minio` / `minio123`

## Команды

```bash
docker compose exec app php yii
docker compose exec app composer install
```

## Что реализовано

- Гость может смотреть книги, авторов, отчет и подписываться на новые книги автора.
- Авторизованный пользователь может добавлять, редактировать и удалять книги и авторов.
- Книга хранит название, год выпуска, описание, ISBN и фото главной страницы в S3-compatible хранилище.
- У книги может быть несколько авторов.
- Отчет показывает ТОП-10 авторов по количеству выпущенных книг за выбранный год.
- При добавлении новой книги подписчикам автора отправляется SMS через SMSPilot в тестовом режиме.

## Окружение

- `app` - PHP-FPM 8.3 с Composer и расширениями для Yii2/MySQL.
- `web` - Nginx, отдающий `web/index.php`.
- `db` - MySQL 8.
- `minio` - локальное S3-compatible хранилище для обложек.

Директории `vendor`, `runtime` и `web/assets` не хранятся в репозитории.
