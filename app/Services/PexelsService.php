<?php
// app\Services\PexelsService.php

namespace App\Services;

use GuzzleHttp\Client;

class PexelsService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.pexels.com/v1/',
            'headers' => [
                'Authorization' => 'I9pLnw7N80C2Ulb67xjum5ZdWWWozQnDTHFUeK9lFMKYpUPY9HcHUfGv',
            ],
        ]);
    }

    public function getRandomPhoto()
    {
        $response = $this->client->request('GET', 'search', [
            'query' => [
                'query' => 'business, nature',
                'per_page' => 1,
                'page' => rand(1, 100), // Número aleatório entre 1 e 100
            ],
        ]);

        $body = $response->getBody();
        $data = json_decode($body, true);

        return $data['photos'][0]['src']['original'] ?? null;
    }
}
