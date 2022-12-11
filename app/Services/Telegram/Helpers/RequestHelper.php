<?php

namespace App\Services\Telegram\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Utils;

class RequestHelper
{
    const API_URL = 'https://api.telegram.org';


    public static function post(string $method, array $params = []): array
    {
        $url = static::getApiUrl() . $method;
        $response = (new Client())->request('POST', $url, $params);

        return Utils::jsonDecode($response->getBody()->getContents(), true);
    }

    public static function get(string $method, array $params = []): array
    {
        $url = static::getApiUrl() . $method . '?' . http_build_query($params);
        $response = (new Client())->request('GET', $url);

        return Utils::jsonDecode($response->getBody()->getContents(), true);
    }

    public static function getApiUrl(): string
    {
        return self::API_URL . '/bot' . env('TELEGRAM_BOT_TOKEN') . '/';
    }
}
