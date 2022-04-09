<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser;

use Panda\ToyJsonParser\Lexer\Lexer;
use Panda\ToyJsonParser\Parser\Parser;

final class JsonParser
{
    public function parse(string $json): array|string|int|bool|null
    {
        $lexer = new Lexer($json);
        $parser = new Parser($lexer);

        return $parser->parse();
    }
}
