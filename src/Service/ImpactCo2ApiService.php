<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ImpactCo2ApiService
{
    private HttpClientInterface $client;
    private string $apiUrl = 'https://impactco2.fr/api/v1';

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getCategories(): array
    {
        $response = $this->client->request('GET', $this->apiUrl . '/thematiques');
        // On récupère le tableau 'data' comme dans le code d'Anano
        return $response->toArray()['data'] ?? [];
    }

    public function getCategoryData(string $categorySlug, array $queryParams = []): array
    {
        $endpointMap = [
            'food' => '/alimentation',
            'transport' => '/transport',
            'heating' => '/chauffage',
            'fruits-and-veggies' => '/fruitsetlegumes',
            'ecv' => '/thematiques/ecv/' . $categorySlug,
        ];

        if (!isset($endpointMap[$categorySlug])) {
            throw new \InvalidArgumentException("Unknown category: $categorySlug");
        }

        $response = $this->client->request('GET', $this->apiUrl . $endpointMap[$categorySlug], [
            'query' => $queryParams
        ]);

        return $response->toArray()['data'] ?? [];
    }
}