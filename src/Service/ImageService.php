<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ImageService
{
    private $client;
    private $params;

    public function __construct(HttpClientInterface $client, string $params)
    {
        $this->client = $client;
        $this->params = $params;
    }

    public function getFilteredImages(int $documentId): array
    {
        $apiUrl = $this->params;

        $response = $this->client->request('GET', $apiUrl . '/api/images', [
            'query' => ['document' => $documentId],
            'verify_peer' => false,  
            'verify_host' => false,
        ]);

        return $response->toArray(); 
    }
}