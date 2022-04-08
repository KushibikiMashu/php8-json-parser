<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Lexer;

final class Lexer
{
    private int $length;
    private int $position;

    public function __construct(private string $json)
    {
        $this->length = mb_strlen($this->json);
        $this->position = 0;
    }

    public function current(): string
    {
        return mb_substr($this->json, $this->position, 1);
    }
}
