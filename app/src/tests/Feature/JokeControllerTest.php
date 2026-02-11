<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\JokeService;
use Mockery;
use Tests\TestCase;

class JokeControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_can_fetch_three_jokes(): void
    {
        $mockJokes = [
            ['id' => 1, 'joke' => 'First joke'],
            ['id' => 2, 'joke' => 'Second joke'],
            ['id' => 3, 'joke' => 'Third joke'],
        ];

        $jokeServiceMock = Mockery::mock(JokeService::class);
        $jokeServiceMock->shouldReceive('fetchJokes')
            ->once()
            ->andReturn($mockJokes);

        $this->app->instance(JokeService::class, $jokeServiceMock);

        $response = $this->getJson('/api/jokes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'joke'],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_returns_500_when_joke_service_fails(): void
    {
        $jokeServiceMock = Mockery::mock(JokeService::class);
        $jokeServiceMock->shouldReceive('fetchJokes')
            ->once()
            ->andThrow(new \Exception('External API error'));

        $this->app->instance(JokeService::class, $jokeServiceMock);

        $response = $this->getJson('/api/jokes');

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'Unable to fetch jokes at this time',
            ]);
    }
}
