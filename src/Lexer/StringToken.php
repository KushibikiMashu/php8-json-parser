<?php

declare(strict_types=1);

namespace panda\ToyJsonParser\Lexer;

final class StringToken implements TokenInterface
{
    public function __construct(#[Immutable] private string $value)
    {
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
