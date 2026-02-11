<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Data\JokeData;
use App\Services\JokeService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class JokeServiceTest extends TestCase
{
    private function mockJokes(): array
    {
        return [
            ['id' => 1, 'type' => 'single', 'setup' => 'Test joke 1', 'punchline' => 'Punchline 1'],
            ['id' => 2, 'type' => 'single', 'setup' => 'Test joke 2', 'punchline' => 'Punchline 2'],
            ['id' => 3, 'type' => 'single', 'setup' => 'Test joke 3', 'punchline' => 'Punchline 3'],
            ['id' => 4, 'type' => 'single', 'setup' => 'Test joke 4', 'punchline' => 'Punchline 4'],
            ['id' => 5, 'type' => 'single', 'setup' => 'Test joke 5', 'punchline' => 'Punchline 5'],
            ['id' => 6, 'type' => 'single', 'setup' => 'Test joke 6', 'punchline' => 'Punchline 6'],
            ['id' => 7, 'type' => 'single', 'setup' => 'Test joke 7', 'punchline' => 'Punchline 7'],
            ['id' => 8, 'type' => 'single', 'setup' => 'Test joke 8', 'punchline' => 'Punchline 8'],
            ['id' => 9, 'type' => 'single', 'setup' => 'Test joke 9', 'punchline' => 'Punchline 9'],
            ['id' => 10, 'type' => 'single', 'setup' => 'Test joke 10', 'punchline' => 'Punchline 10'],
        ];
    }

    public function test_can_fetch_three_jokes(): void
    {
        Http::fake([
            'official-joke-api.appspot.com/*' => Http::response($this->mockJokes(), 200),
        ]);

        $service = new JokeService;
        $jokes = $service->fetchJokes();

        $this->assertCount(3, $jokes);
        foreach ($jokes as $joke) {
            $this->assertInstanceOf(JokeData::class, $joke);
        }
    }

    public function test_randomizes_jokes_from_larger_set(): void
    {
        Http::fake([
            'official-joke-api.appspot.com/*' => Http::response($this->mockJokes(), 200),
        ]);

        $service = new JokeService;

        $allResults = [];
        for ($i = 0; $i < 10; $i++) {
            $jokes = $service->fetchJokes();
            $ids = array_map(fn (JokeData $j): int => $j->id, $jokes);
            sort($ids);
            $allResults[] = implode(',', $ids);
        }

        $uniqueResults = array_unique($allResults);
        $this->assertGreaterThan(1, count($uniqueResults), 'Jokes should be randomized across calls');
    }

    public function test_returns_error_when_api_fails_4xx(): void
    {
        Http::fake([
            'official-joke-api.appspot.com/*' => Http::response(['error' => 'Not found'], 404),
        ]);

        $service = new JokeService;
        $jokes = $service->fetchJokes();

        $this->assertEmpty($jokes);
    }

    public function test_returns_error_when_api_fails_5xx(): void
    {
        Http::fake([
            'official-joke-api.appspot.com/*' => Http::response(['error' => 'Internal server error'], 500),
        ]);

        $service = new JokeService;
        $jokes = $service->fetchJokes();

        $this->assertEmpty($jokes);
    }

    public function test_handles_network_timeout(): void
    {
        Http::fake([
            'official-joke-api.appspot.com/*' => Http::failedConnection('Connection timed out'),
        ]);

        $service = new JokeService;
        $jokes = $service->fetchJokes();

        $this->assertEmpty($jokes);
    }

    public function test_handles_invalid_api_response(): void
    {
        Http::fake([
            'official-joke-api.appspot.com/*' => Http::response('invalid json', 200),
        ]);

        $service = new JokeService;
        $jokes = $service->fetchJokes();

        $this->assertEmpty($jokes);
    }
}
