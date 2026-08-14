<?php

namespace app\services;

use RuntimeException;
use yii\web\UploadedFile;

class S3Storage
{
    public function __construct(
        private readonly array $config
    ) {
    }

    /**
     * Загружает файл в S3-compatible хранилище.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return string
     */
    public function upload(UploadedFile $file, string $directory): string
    {
        $key = trim($directory, '/') . '/' . date('Y/m') . '/' . bin2hex(random_bytes(16)) . '.' . $file->extension;
        $content = file_get_contents($file->tempName);

        if ($content === false) {
            throw new RuntimeException('Не удалось прочитать загруженный файл.');
        }

        $this->putObject($key, $content, $file->type ?: 'application/octet-stream');

        return $this->publicUrl($key);
    }

    private function putObject(string $key, string $content, string $contentType): void
    {
        $endpoint = rtrim((string) $this->config['endpoint'], '/');
        $bucket = (string) $this->config['bucket'];
        $url = $endpoint . '/' . rawurlencode($bucket) . '/' . str_replace('%2F', '/', rawurlencode($key));

        $headers = $this->signedHeaders('PUT', $bucket, $key, $content, $contentType);
        $context = stream_context_create([
            'http' => [
                'method' => 'PUT',
                'header' => $this->formatHeaders($headers),
                'content' => $content,
                'ignore_errors' => true,
                'timeout' => 15,
            ],
        ]);

        $result = file_get_contents($url, false, $context);
        $statusLine = $http_response_header[0] ?? '';

        if ($result === false || !str_contains($statusLine, '200')) {
            throw new RuntimeException('S3 не принял файл: ' . ($statusLine ?: 'нет ответа'));
        }
    }

    private function signedHeaders(string $method, string $bucket, string $key, string $content, string $contentType): array
    {
        $endpoint = parse_url((string) $this->config['endpoint']);
        $host = $endpoint['host'] . (isset($endpoint['port']) ? ':' . $endpoint['port'] : '');
        $region = (string) $this->config['region'];
        $accessKey = (string) $this->config['accessKey'];
        $secretKey = (string) $this->config['secretKey'];
        $amzDate = gmdate('Ymd\THis\Z');
        $date = gmdate('Ymd');
        $payloadHash = hash('sha256', $content);
        $canonicalUri = '/' . rawurlencode($bucket) . '/' . str_replace('%2F', '/', rawurlencode($key));
        $credentialScope = $date . '/' . $region . '/s3/aws4_request';

        $canonicalHeaders = implode("\n", [
            'content-type:' . $contentType,
            'host:' . $host,
            'x-amz-content-sha256:' . $payloadHash,
            'x-amz-date:' . $amzDate,
            '',
        ]);
        $signedHeaders = 'content-type;host;x-amz-content-sha256;x-amz-date';
        $canonicalRequest = implode("\n", [
            $method,
            $canonicalUri,
            '',
            $canonicalHeaders,
            $signedHeaders,
            $payloadHash,
        ]);
        $stringToSign = implode("\n", [
            'AWS4-HMAC-SHA256',
            $amzDate,
            $credentialScope,
            hash('sha256', $canonicalRequest),
        ]);

        $signingKey = $this->signingKey($secretKey, $date, $region);
        $signature = hash_hmac('sha256', $stringToSign, $signingKey);

        return [
            'Authorization' => 'AWS4-HMAC-SHA256 Credential=' . $accessKey . '/' . $credentialScope
                . ', SignedHeaders=' . $signedHeaders . ', Signature=' . $signature,
            'Content-Type' => $contentType,
            'Host' => $host,
            'X-Amz-Content-Sha256' => $payloadHash,
            'X-Amz-Date' => $amzDate,
        ];
    }

    private function signingKey(string $secretKey, string $date, string $region): string
    {
        $dateKey = hash_hmac('sha256', $date, 'AWS4' . $secretKey, true);
        $dateRegionKey = hash_hmac('sha256', $region, $dateKey, true);
        $dateRegionServiceKey = hash_hmac('sha256', 's3', $dateRegionKey, true);

        return hash_hmac('sha256', 'aws4_request', $dateRegionServiceKey, true);
    }

    private function formatHeaders(array $headers): string
    {
        $lines = [];

        foreach ($headers as $name => $value) {
            $lines[] = $name . ': ' . $value;
        }

        return implode("\r\n", $lines);
    }

    private function publicUrl(string $key): string
    {
        return rtrim((string) $this->config['publicEndpoint'], '/')
            . '/' . rawurlencode((string) $this->config['bucket'])
            . '/' . str_replace('%2F', '/', rawurlencode($key));
    }
}
