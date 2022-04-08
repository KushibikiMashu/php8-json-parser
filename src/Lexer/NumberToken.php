<?php

declare(strict_types=1);

namespace panda\ToyJsonParser\Lexer;

final class NumberToken implements TokenInterface
{
    public function __construct(#[Immutable] private int $value)
    {
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
