<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AltusService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        // Intentionally left blank; Http client used in methods.
    }

    /**
     * Execute a search request against the Altus API.
     */
    public function search(array $payload): ?array
    {
        $response = $this->request('POST', '/search', $payload);

        return $response;
    }

    /**
     * Get search suggestions.
     */
    public function suggestions(string $query, array $databases = [], int $limit = 5): ?array
    {
        $params = array_filter([
            'query' => $query,
            'databases' => $databases,
            'limit' => $limit,
        ]);

        return $this->request('GET', '/search/suggestions', [], $params);
    }

    /**
     * Analyze a search query for performance recommendations.
     */
    public function analyze(array $payload): ?array
    {
        return $this->request('POST', '/search/analyze', $payload);
    }

    /**
     * Get search history.
     */
    public function history(int $limit = 20, int $offset = 0): ?array
    {
        $params = ['limit' => $limit, 'offset' => $offset];

        return $this->request('GET', '/search/history', [], $params);
    }

    /**
     * Get search trends.
     */
    public function trends(): ?array
    {
        return $this->request('GET', '/search/trends');
    }

    /**
     * Internal helper to perform HTTP requests to Altus API.
     */
    protected function request(string $method, string $path, array $body = [], array $query = []): ?array
    {
        $base = config('services.altus.base_url');
        $apiKey = config('services.altus.api_key');

        if (empty($base) || empty($apiKey)) {
            logger()->error('AltusService: missing configuration for altus api');
            return null;
        }

        try {
            $client = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Accept' => 'application/json',
            ])->timeout(30);

            if (strtoupper($method) === 'GET') {
                $response = $client->get("{$base}{$path}", $query);
            } elseif (strtoupper($method) === 'POST') {
                $response = $client->post("{$base}{$path}", $body);
            } else {
                $response = $client->send($method, "{$base}{$path}", [
                    'json' => $body,
                    'query' => $query,
                ]);
            }

            if ($response->successful()) {
                return $response->json();
            }

            logger()->warning('AltusService: non-success response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $response->json() ?? null;
        } catch (\Exception $e) {
            logger()->error('AltusService request failed: '.$e->getMessage());
            return null;
        }
    }
}
