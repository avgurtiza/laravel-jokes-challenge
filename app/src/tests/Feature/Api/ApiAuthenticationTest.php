<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Data\JokeData;
use App\Models\User;
use App\Services\JokeService;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
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

    public function test_api_returns_401_without_token(): void
    {
        $response = $this->getJson('/api/jokes');

        $response->assertStatus(401);
    }

    public function test_api_returns_401_with_invalid_token(): void
    {
        $response = $this->getJson('/api/jokes', [
            'Authorization' => 'Bearer invalid-token',
        ]);

        $response->assertStatus(401);
    }

    public function test_api_returns_jokes_with_valid_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $jokeServiceMock = Mockery::mock(JokeService::class);
        $jokeServiceMock->shouldReceive('fetchJokes')
            ->once()
            ->andReturn($this->mockJokes());
        $this->app->instance(JokeService::class, $jokeServiceMock);

        $response = $this->getJson('/api/jokes', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_api_returns_correct_json_structure_with_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $jokeServiceMock = Mockery::mock(JokeService::class);
        $jokeServiceMock->shouldReceive('fetchJokes')
            ->once()
            ->andReturn($this->mockJokes());
        $this->app->instance(JokeService::class, $jokeServiceMock);

        $response = $this->getJson('/api/jokes', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'type', 'setup', 'punchline'],
                ],
            ]);
    }

    public function test_api_returns_500_when_service_fails_with_valid_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $jokeServiceMock = Mockery::mock(JokeService::class);
        $jokeServiceMock->shouldReceive('fetchJokes')
            ->once()
            ->andThrow(new \Exception('Service unavailable'));
        $this->app->instance(JokeService::class, $jokeServiceMock);

        $response = $this->getJson('/api/jokes', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'Unable to fetch jokes at this time',
            ]);
    }

    public function test_api_works_with_sanctum_acting_as(): void
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

    public function test_revoked_token_returns_401(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token');
        $plainTextToken = $token->plainTextToken;

        $token->accessToken->delete();

        $response = $this->getJson('/api/jokes', [
            'Authorization' => 'Bearer '.$plainTextToken,
        ]);

        $response->assertStatus(401);
    }
}
