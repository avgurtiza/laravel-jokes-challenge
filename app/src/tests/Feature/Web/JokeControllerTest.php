<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

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

    private function mockJokes(): array
    {
        return [
            ['id' => 1, 'type' => 'programming', 'joke' => 'First joke'],
            ['id' => 2, 'type' => 'programming', 'setup' => 'Setup line', 'punchline' => 'Punchline'],
            ['id' => 3, 'type' => 'programming', 'joke' => 'Third joke'],
        ];
    }

    public function test_web_page_loads_successfully(): void
    {
        $jokeServiceMock = Mockery::mock(JokeService::class);
        $jokeServiceMock->shouldReceive('fetchJokes')
            ->once()
            ->andReturn($this->mockJokes());

        $this->app->instance(JokeService::class, $jokeServiceMock);

        $response = $this->get('/jokes');

        $response->assertStatus(200)
            ->assertViewIs('jokes.index');
    }

    public function test_web_displays_all_jokes(): void
    {
        $jokeServiceMock = Mockery::mock(JokeService::class);
        $jokeServiceMock->shouldReceive('fetchJokes')
            ->once()
            ->andReturn($this->mockJokes());

        $this->app->instance(JokeService::class, $jokeServiceMock);

        $response = $this->get('/jokes');

        $response->assertStatus(200)
            ->assertSee('First joke')
            ->assertSee('Setup line')
            ->assertSee('Punchline')
            ->assertSee('Third joke');
    }

    public function test_web_displays_error_message_on_failure(): void
    {
        $jokeServiceMock = Mockery::mock(JokeService::class);
        $jokeServiceMock->shouldReceive('fetchJokes')
            ->once()
            ->andThrow(new \Exception('API error'));

        $this->app->instance(JokeService::class, $jokeServiceMock);

        $response = $this->get('/jokes');

        $response->assertStatus(200)
            ->assertSee('Unable to fetch jokes at this time. Please try again.');
    }

    public function test_web_passes_correct_data_to_view(): void
    {
        $mockJokes = $this->mockJokes();

        $jokeServiceMock = Mockery::mock(JokeService::class);
        $jokeServiceMock->shouldReceive('fetchJokes')
            ->once()
            ->andReturn($mockJokes);

        $this->app->instance(JokeService::class, $jokeServiceMock);

        $response = $this->get('/jokes');

        $response->assertStatus(200)
            ->assertViewHas('jokes', $mockJokes)
            ->assertViewHas('error', null);
    }
}
