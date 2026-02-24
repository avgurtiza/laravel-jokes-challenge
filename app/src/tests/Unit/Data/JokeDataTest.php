<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use App\Data\JokeData;
use InvalidArgumentException;
use Tests\TestCase;

class JokeDataTest extends TestCase
{
    public function test_creates_from_valid_two_part_joke_array(): void
    {
        $data = [
            'id' => 1,
            'type' => 'twopart',
            'setup' => 'Why did the programmer quit?',
            'punchline' => 'Because he didnt array.',
        ];

        $jokeData = JokeData::fromArray($data);

        $this->assertSame(1, $jokeData->id);
        $this->assertSame('twopart', $jokeData->type);
        $this->assertSame('Why did the programmer quit?', $jokeData->setup);
        $this->assertSame('Because he didnt array.', $jokeData->punchline);
    }

    public function test_throws_exception_when_type_missing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required key: type');

        JokeData::fromArray(['id' => 1, 'setup' => 'test']);
    }

    public function test_throws_exception_when_id_missing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required key: id');

        JokeData::fromArray(['type' => 'test', 'setup' => 'test', 'punchline' => 'test']);
    }

    public function test_is_immutable(): void
    {
        $jokeData = JokeData::fromArray([
            'id' => 1,
            'type' => 'test',
            'setup' => 'test',
            'punchline' => 'test',
        ]);

        $this->expectException(\Error::class);
        $jokeData->id = 2; // Should throw Error: Cannot modify readonly property
    }

    public function test_json_serializes_two_part_joke_correctly(): void
    {
        $jokeData = JokeData::fromArray([
            'id' => 1,
            'type' => 'twopart',
            'setup' => 'Setup',
            'punchline' => 'Punchline',
        ]);

        $json = json_encode($jokeData);
        $decoded = json_decode($json, true);

        $this->assertSame(1, $decoded['id']);
        $this->assertSame('twopart', $decoded['type']);
        $this->assertSame('Setup', $decoded['setup']);
        $this->assertSame('Punchline', $decoded['punchline']);
        $this->assertArrayNotHasKey('joke', $decoded);
    }
}
