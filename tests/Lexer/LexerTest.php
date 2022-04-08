<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test\Lexer;

use Panda\ToyJsonParser\Lexer\Lexer;
use PHPUnit\Framework\TestCase;

final class LexerTest extends TestCase
{
    /**
     * @test
     */
    public function current_現在位置の文字を1文字取得()
    {
        $lexer = new Lexer('{ "foo": "bar" }');
        $actual = $lexer->current();
        $expected = "{";
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function getNextToken_次の文字を1文字取得()
    {
        $lexer = new Lexer('{ "foo": "bar" }');
        $actual = $lexer->getNextToken();
        $expected = " ";
        $this->assertEquals($expected, $actual);
    }
}
