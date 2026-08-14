<?php

return [
    's3' => [
        'endpoint' => getenv('S3_ENDPOINT') ?: 'http://minio:9000',
        'publicEndpoint' => getenv('S3_PUBLIC_ENDPOINT') ?: 'http://localhost:9000',
        'bucket' => getenv('S3_BUCKET') ?: 'book-covers',
        'region' => getenv('S3_REGION') ?: 'us-east-1',
        'accessKey' => getenv('S3_ACCESS_KEY') ?: 'minio',
        'secretKey' => getenv('S3_SECRET_KEY') ?: 'minio123',
    ],
    'smsPilot' => [
        'apiKey' => getenv('SMSPILOT_API_KEY') ?: 'XXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZXXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZ',
        'test' => (bool) (getenv('SMSPILOT_TEST') ?: true),
        'apiUrl' => 'https://smspilot.ru/api.php',
    ],
];
