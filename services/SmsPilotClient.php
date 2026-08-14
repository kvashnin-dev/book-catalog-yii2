<?php

namespace app\services;

use RuntimeException;

class SmsPilotClient
{
    public function __construct(
        private readonly array $config
    ) {
    }

    /**
     * Отправляет SMS через SMSPilot.
     *
     * @param string $phone
     * @param string $text
     * @return void
     */
    public function send(string $phone, string $text): void
    {
        $params = [
            'send' => $text,
            'to' => $phone,
            'apikey' => (string) $this->config['apiKey'],
            'format' => 'json',
        ];

        if (!empty($this->config['test'])) {
            $params['test'] = '1';
        }

        $url = rtrim((string) $this->config['apiUrl'], '?') . '?' . http_build_query($params);
        $response = file_get_contents($url, false, stream_context_create([
            'http' => [
                'timeout' => 10,
                'ignore_errors' => true,
            ],
        ]));

        if ($response === false) {
            throw new RuntimeException('SMSPilot не ответил на запрос.');
        }

        $data = json_decode($response, true);

        if (isset($data['error'])) {
            $message = $data['error']['description_ru'] ?? $data['error']['description'] ?? 'неизвестная ошибка';
            throw new RuntimeException('SMSPilot вернул ошибку: ' . $message);
        }
    }
}
