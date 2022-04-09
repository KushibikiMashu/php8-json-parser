<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Parser;

use Panda\ToyJsonParser\Lexer\Lexer;
use Panda\ToyJsonParser\Lexer\Token\EofToken;

final class Parser
{
    public function __construct(private Lexer $lexer)
    {
    }

    public function parse(): array|string|int|float|bool|null
    {
        $token = $this->lexer->getNextToken();
        $ret = ValueParser::parse($this->lexer, $token);

        if ($this->lexer->getNextToken() instanceof EofToken) {
            return $ret;
        }

        throw new ParserException(message: 'Unparsed tokens detected.');
    }
}
