<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class WebJokeControllerTest extends TestCase
{
    public function test_can_view_jokes_page(): void
    {
        $response = $this->get('/jokes');

        $response->assertStatus(200)
            ->assertViewIs('jokes.index')
            ->assertViewHas('jokes', [])
            ->assertViewHas('error', null)
            ->assertSee('Programming Jokes')
            ->assertSee('Refresh Jokes');
    }

    public function test_refresh_button_has_loading_spinner(): void
    {
        $response = $this->get('/jokes');

        $response->assertStatus(200)
            ->assertSee('id="jokes-container"', false)
            ->assertSee('animate-spin', false)
            ->assertSee('Loading...', false);
    }

    public function test_refresh_button_calls_javascript_function(): void
    {
        $response = $this->get('/jokes');

        $response->assertStatus(200)
            ->assertSee('Refresh Jokes')
            ->assertSee('refreshJokes()');
    }

    public function test_page_loads_ajax_on_dom_ready(): void
    {
        $response = $this->get('/jokes');

        $response->assertStatus(200)
            ->assertSee("document.addEventListener('DOMContentLoaded'", false)
            ->assertSee('refreshJokes()', false);
    }
}
