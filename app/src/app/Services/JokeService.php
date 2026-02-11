<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\JokeData;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JokeService
{
    /**
     * Fetch 3 random programming jokes from the external API.
     *
     * @return array<int, JokeData>
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

            shuffle($jokes);

            $selectedJokes = array_slice($jokes, 0, 3);

            return array_map(
                fn (array $joke): JokeData => JokeData::fromArray($joke),
                $selectedJokes
            );
        } catch (\Throwable $e) {
            Log::error('Failed to fetch jokes from API', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}
