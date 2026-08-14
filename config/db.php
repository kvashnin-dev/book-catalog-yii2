<?php

return [
    'class' => yii\db\Connection::class,
    'dsn' => sprintf(
        'mysql:host=%s;dbname=%s',
        getenv('DB_HOST') ?: 'db',
        getenv('MYSQL_DATABASE') ?: 'book_catalog'
    ),
    'username' => getenv('MYSQL_USER') ?: 'book_catalog',
    'password' => getenv('MYSQL_PASSWORD') ?: 'book_catalog',
    'charset' => 'utf8mb4',
];
