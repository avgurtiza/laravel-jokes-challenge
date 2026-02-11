<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use Tests\TestCase;

class JokeControllerTest extends TestCase
{
    public function test_web_page_loads_successfully(): void
    {
        $response = $this->get('/jokes');

        $response->assertStatus(200)
            ->assertViewIs('jokes.index');
    }

    public function test_web_passes_empty_jokes_to_view(): void
    {
        $response = $this->get('/jokes');

        $response->assertStatus(200)
            ->assertViewHas('jokes', [])
            ->assertViewHas('error', null);
    }

    public function test_web_page_has_ajax_refresh_elements(): void
    {
        $response = $this->get('/jokes');

        $response->assertStatus(200)
            ->assertSee('id="jokes-container"', false)
            ->assertSee('id="refresh-btn"', false)
            ->assertSee('animate-spin', false)
            ->assertSee('Loading...', false)
            ->assertSee('DOMContentLoaded', false)
            ->assertSee('refreshJokes()', false);
    }

    public function test_web_page_calls_refresh_on_page_load(): void
    {
        $response = $this->get('/jokes');

        $response->assertStatus(200)
            ->assertSee("document.addEventListener('DOMContentLoaded'", false)
            ->assertSee('refreshJokes()', false);
    }
}
