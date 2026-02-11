<?php

declare(strict_types=1);

namespace App\Data;

use InvalidArgumentException;
use JsonSerializable;

readonly class JokeData implements JsonSerializable
{
    public function __construct(
        public int $id,
        public string $type,
        public string $setup,
        public string $punchline,
    ) {}

    public static function fromArray(array $data): self
    {
        $requiredKeys = ['id', 'type', 'setup', 'punchline'];

        foreach ($requiredKeys as $key) {
            if (! array_key_exists($key, $data)) {
                throw new InvalidArgumentException("Missing required key: {$key}");
            }

            if (! is_string($data[$key]) && ! is_int($data[$key])) {
                throw new InvalidArgumentException("Key '{$key}' must be a string or int");
            }
        }

        return new self(
            id: is_int($data['id']) ? $data['id'] : (int) $data['id'],
            type: (string) $data['type'],
            setup: (string) $data['setup'],
            punchline: (string) $data['punchline'],
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'setup' => $this->setup,
            'punchline' => $this->punchline,
        ];
    }
}
