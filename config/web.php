<?php

use app\services\BookService;
use app\services\S3Storage;
use app\services\SmsPilotClient;
use app\services\SubscriptionNotifier;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'book-catalog',
    'basePath' => dirname(__DIR__),
    'language' => 'ru-RU',
    'sourceLanguage' => 'ru-RU',
    'container' => [
        'definitions' => [
            S3Storage::class => static fn (): S3Storage => new S3Storage($params['s3']),
            SmsPilotClient::class => static fn (): SmsPilotClient => new SmsPilotClient($params['smsPilot']),
            SubscriptionNotifier::class => static fn ($container): SubscriptionNotifier => new SubscriptionNotifier(
                $container->get(SmsPilotClient::class)
            ),
            BookService::class => static fn ($container): BookService => new BookService(
                $container->get(S3Storage::class),
                $container->get(SubscriptionNotifier::class)
            ),
        ],
    ],
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => getenv('YII_COOKIE_VALIDATION_KEY') ?: 'dev-cookie-validation-key',
        ],
        'user' => [
            'identityClass' => app\models\User::class,
            'enableAutoLogin' => true,
            'loginUrl' => ['auth/login'],
        ],
        'cache' => [
            'class' => yii\caching\FileCache::class,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'book/index',
                'login' => 'auth/login',
                'logout' => 'auth/logout',
                'signup' => 'auth/signup',
                'books/<id:\d+>' => 'book/view',
                'books' => 'book/index',
                'authors/<id:\d+>' => 'author/view',
                'authors' => 'author/index',
                'reports/authors' => 'report/authors',
            ],
        ],
    ],
    'params' => $params,
];

return $config;
