<?php
// src/Service/ImpactCo2ApiService.php
namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ImpactCo2ApiService
{
    private HttpClientInterface $client;
    private LoggerInterface $logger;
    private string $apiUrl = 'https://impactco2.fr/api/v1';

    public function __construct(HttpClientInterface $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * Get all available categories (thématiques)
     */
    public function getCategories(): array
    {
        try {
            $response = $this->client->request('GET', $this->apiUrl . '/thematiques');
            return $response->toArray()['data'] ?? [];
        } catch (\Exception $e) {
            $this->logger->error('[ImpactCo2ApiService] getCategories failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get data for a specific category
     *
     * @param string $categorySlug slug of the category (food, transport, etc.)
     * @param array $queryParams optional query parameters
     * @return array
     */
    public function getCategoryData(string $categorySlug, array $queryParams = []): array
    {
        $endpointMap = [
            'food'             => '/alimentation',
            'transport'        => '/transport',
            'heating'          => '/chauffage',
            'fruits-and-veggies' => '/fruitsetlegumes',
            'ecv'              => '/thematiques/ecv/' . $categorySlug,
        ];

        if (!isset($endpointMap[$categorySlug])) {
            throw new \InvalidArgumentException("Unknown category: $categorySlug");
        }

        $endpoint = $endpointMap[$categorySlug];

        try {
            $response = $this->client->request('GET', $this->apiUrl . $endpoint, [
                'query' => $queryParams
            ]);

            return $response->toArray()['data'] ?? [];
        } catch (\Exception $e) {
            $this->logger->error('[ImpactCo2ApiService] getCategoryData failed for "' . $categorySlug . '": ' . $e->getMessage());
            return [];
        }
    }
}
