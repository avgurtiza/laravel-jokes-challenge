<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JokeService
{
    /**
     * Fetch 3 random programming jokes from the external API.
     *
     * @return array<int, array<string, mixed>>
     */
    public function fetchJokes(): array
    {
        $url = config('services.joke_api.url');

        try {
            $response = Http::timeout(30)
                ->get($url);

            $response->throw();

            $jokes = $response->json();

            if (! is_array($jokes) || count($jokes) === 0) {
                Log::warning('Invalid response from joke API', [
                    'url' => $url,
                    'response' => $jokes,
                ]);

                return [];
            }

            // Shuffle and return first 3 jokes
            shuffle($jokes);

            return array_slice($jokes, 0, 3);
        } catch (\Throwable $e) {
            Log::error('Failed to fetch jokes from API', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}
