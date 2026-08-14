<?php

use app\repositories\AuthorReportRepository;
use app\repositories\AuthorRepository;
use app\repositories\BookRepository;
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
        AuthorRepository::class => AuthorRepository::class,
        BookRepository::class => BookRepository::class,
        AuthorReportRepository::class => AuthorReportRepository::class,
        S3Storage::class => static fn (): S3Storage => Yii::$container->get(S3Storage::class, [$params['s3']]),
        SmsPilotClient::class => static fn (): SmsPilotClient => Yii::$container->get(SmsPilotClient::class, [$params['smsPilot']]),
        SubscriptionNotifier::class => static fn (): SubscriptionNotifier => Yii::$container->get(SubscriptionNotifier::class, [
            Yii::$app->get(SmsPilotClient::class),
        ]),
        BookService::class => static fn (): BookService => Yii::$container->get(BookService::class, [
            Yii::$app->get(S3Storage::class),
            Yii::$app->get(SubscriptionNotifier::class),
        ]),
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
