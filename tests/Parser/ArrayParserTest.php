<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test\Parser;

use Panda\ToyJsonParser\Lexer\Lexer;
use Panda\ToyJsonParser\Parser\ArrayParser;
use PHPUnit\Framework\TestCase;

final class ArrayParserTest extends TestCase
{
    /**
     * @test @skip
     */
    public function parse__jsonの配列をPHPの配列に変換する(): void
    {
        // [ は ValueParser で消費済みのため、ArrayParser には以下の文字列が渡される
        $input = '"foo", 1, null, true, false]';
        $lexer = new Lexer($input);
        $actual = ArrayParser::parse($lexer);
        $expected = ['foo', 1, null, true, false];
        $this->assertEquals($expected, $actual);

    }
}
