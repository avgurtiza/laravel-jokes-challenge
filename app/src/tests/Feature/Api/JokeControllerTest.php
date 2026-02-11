<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Data\JokeData;
use App\Models\User;
use App\Services\JokeService;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class JokeControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function mockJokes(): array
    {
        return [
            JokeData::fromArray(['id' => 1, 'type' => 'programming', 'setup' => 'Test joke 1', 'punchline' => 'Punchline 1']),
            JokeData::fromArray(['id' => 2, 'type' => 'programming', 'setup' => 'Test joke 2', 'punchline' => 'Punchline 2']),
            JokeData::fromArray(['id' => 3, 'type' => 'programming', 'setup' => 'Test joke 3', 'punchline' => 'Punchline 3']),
        ];
    }

    public function test_api_returns_jokes_on_success(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $jokeServiceMock = Mockery::mock(JokeService::class);
        $jokeServiceMock->shouldReceive('fetchJokes')
            ->once()
            ->andReturn($this->mockJokes());

        $this->app->instance(JokeService::class, $jokeServiceMock);

        $response = $this->getJson('/api/jokes');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_api_returns_correct_json_structure(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $jokeServiceMock = Mockery::mock(JokeService::class);
        $jokeServiceMock->shouldReceive('fetchJokes')
            ->once()
            ->andReturn($this->mockJokes());

        $this->app->instance(JokeService::class, $jokeServiceMock);

        $response = $this->getJson('/api/jokes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'type', 'setup', 'punchline'],
                ],
            ]);
    }

    public function test_api_returns_500_when_service_fails(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $jokeServiceMock = Mockery::mock(JokeService::class);
        $jokeServiceMock->shouldReceive('fetchJokes')
            ->once()
            ->andThrow(new \Exception('Service unavailable'));

        $this->app->instance(JokeService::class, $jokeServiceMock);

        $response = $this->getJson('/api/jokes');

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'Unable to fetch jokes at this time',
            ]);
    }

    public function test_api_returns_empty_array_when_no_jokes(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $jokeServiceMock = Mockery::mock(JokeService::class);
        $jokeServiceMock->shouldReceive('fetchJokes')
            ->once()
            ->andReturn([]);

        $this->app->instance(JokeService::class, $jokeServiceMock);

        $response = $this->getJson('/api/jokes');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [],
            ])
            ->assertJsonCount(0, 'data');
    }
}
