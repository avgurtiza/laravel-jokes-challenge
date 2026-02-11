<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\JokeService;
use Mockery;
use Tests\TestCase;

class WebJokeControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_can_view_jokes_page(): void
    {
        $mockJokes = [
            ['id' => 1, 'type' => 'general', 'setup' => 'Why do programmers prefer dark mode?', 'punchline' => 'Because light attracts bugs!'],
            ['id' => 2, 'type' => 'general', 'joke' => 'There are 10 types of people in the world: those who understand binary, and those who don\'t.'],
            ['id' => 3, 'type' => 'general', 'setup' => 'How many programmers does it take to change a light bulb?', 'punchline' => 'None. It\'s a hardware problem.'],
        ];

        $jokeServiceMock = Mockery::mock(JokeService::class);
        $jokeServiceMock->shouldReceive('fetchJokes')
            ->once()
            ->andReturn($mockJokes);

        $this->app->instance(JokeService::class, $jokeServiceMock);

        $response = $this->get('/jokes');

        $response->assertStatus(200)
            ->assertViewIs('jokes.index')
            ->assertViewHas('jokes', $mockJokes)
            ->assertViewHas('error', null)
            ->assertSee('Programming Jokes')
            ->assertSee('Why do programmers prefer dark mode?')
            ->assertSee('Because light attracts bugs!')
            ->assertSee('Refresh Jokes');
    }

    public function test_displays_three_jokes_on_page(): void
    {
        $mockJokes = [
            ['id' => 1, 'type' => 'general', 'joke' => 'First joke'],
            ['id' => 2, 'type' => 'general', 'joke' => 'Second joke'],
            ['id' => 3, 'type' => 'general', 'joke' => 'Third joke'],
        ];

        $jokeServiceMock = Mockery::mock(JokeService::class);
        $jokeServiceMock->shouldReceive('fetchJokes')
            ->once()
            ->andReturn($mockJokes);

        $this->app->instance(JokeService::class, $jokeServiceMock);

        $response = $this->get('/jokes');

        $response->assertStatus(200)
            ->assertSee('First joke')
            ->assertSee('Second joke')
            ->assertSee('Third joke');
    }

    public function test_refresh_button_functionality(): void
    {
        $mockJokes = [
            ['id' => 1, 'type' => 'general', 'joke' => 'Test joke'],
        ];

        $jokeServiceMock = Mockery::mock(JokeService::class);
        $jokeServiceMock->shouldReceive('fetchJokes')
            ->once()
            ->andReturn($mockJokes);

        $this->app->instance(JokeService::class, $jokeServiceMock);

        $response = $this->get('/jokes');

        $response->assertStatus(200)
            ->assertSee('Refresh Jokes')
            ->assertSee('refreshJokes()');
    }

    public function test_handles_error_when_api_fails(): void
    {
        $jokeServiceMock = Mockery::mock(JokeService::class);
        $jokeServiceMock->shouldReceive('fetchJokes')
            ->once()
            ->andThrow(new \Exception('External API error'));

        $this->app->instance(JokeService::class, $jokeServiceMock);

        $response = $this->get('/jokes');

        $response->assertStatus(200)
            ->assertViewIs('jokes.index')
            ->assertViewHas('error', 'Unable to fetch jokes at this time. Please try again.')
            ->assertSee('Unable to fetch jokes at this time. Please try again.')
            ->assertDontSee('class="joke"');
    }

    public function test_handles_empty_jokes_response(): void
    {
        $jokeServiceMock = Mockery::mock(JokeService::class);
        $jokeServiceMock->shouldReceive('fetchJokes')
            ->once()
            ->andReturn([]);

        $this->app->instance(JokeService::class, $jokeServiceMock);

        $response = $this->get('/jokes');

        $response->assertStatus(200)
            ->assertViewIs('jokes.index')
            ->assertViewHas('error', 'Unable to fetch jokes at this time. Please try again.')
            ->assertSee('Unable to fetch jokes at this time. Please try again.')
            ->assertSee('Refresh Jokes');
    }
}
