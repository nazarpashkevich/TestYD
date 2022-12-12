<?php

namespace App\Services\Trello;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Utils;

class Client
{
    const API_BASE_URL = 'https://trello.com/';

    public function getListCards(string $listId): array
    {
        $method = '1/lists/' . $listId . '/cards';
        return $this->request($method);
    }

    public function getMemberData(string $memberId): array
    {
        $method = "1/members/$memberId";
        return $this->request($method);
    }

    public function request($method, $data = []): array
    {
        $url = self::API_BASE_URL . $method . '?' . http_build_query(array_merge($data, $this->getBaseApiParams()));
        try {
            $response = (new \GuzzleHttp\Client())->request('GET', $url);
            return Utils::jsonDecode($response->getBody()->getContents(), true);
        } catch (ClientException $e) {
            return [];
        }
    }

    public function getBaseApiParams(): array
    {
        return ['token' => env('TRELLO_API_TOKEN'), 'key' => env('TRELLO_API_KEY')];
    }
}
