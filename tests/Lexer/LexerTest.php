<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test\Lexer;

use Panda\ToyJsonParser\Lexer\Lexer;
use Panda\ToyJsonParser\Lexer\Token\{
    ColonToken,
    CommaToken,
    FalseToken,
    LeftCurlyBracketToken,
    LeftSquareBracketToken,
    NullToken,
    NumberToken,
    RightCurlyBracketToken,
    RightSquareBracketToken,
    StringToken,
    TrueToken,
};
use PHPUnit\Framework\TestCase;

final class LexerTest extends TestCase
{
    /**
     * @test
     */
    public function current__positionが0のとき、波括弧を返す()
    {
        $lexer = new Lexer('{}');
        $actual = $lexer->current();
        $expected = '{';
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function getNextToken($input, $expected)
    {
        $lexer = new Lexer($input);
        $actual = $lexer->getNextToken();
        $this->assertInstanceOf($expected, $actual);
    }

    public function dataProvider(): array
    {
        return [
            '文字が [ の時、LeftSquareBracketToken のインスタンスを返す' => [
                '[',
                LeftSquareBracketToken::class,
            ],
            '文字が ] の時、RightSquareBracketToken のインスタンスを返す' => [
                ']',
                RightSquareBracketToken::class,
            ],
            '文字が { の時、LeftCurlyBracketToken のインスタンスを返す' => [
                '{',
                LeftCurlyBracketToken::class,
            ],
            '文字が } の時、RightCurlyBracketToken のインスタンスを返す' => [
                '}',
                RightCurlyBracketToken::class,
            ],
            '文字が : の時、ColonToken のインスタンスを返す' => [
                ':',
                ColonToken::class,
            ],
            '文字が , の時、CommaToken のインスタンスを返す' => [
                ',',
                CommaToken::class,
            ],
            '文字が "test" の時、StringToken のインスタンスを返す' => [
                '"test"',
                StringToken::class,
            ],
            '文字が 0 の時、NumberToken のインスタンスを返す' => [
                '0',
                NumberToken::class,
            ],
            '文字が true の時、TrueToken のインスタンスを返す' => [
                'true',
                TrueToken::class,
            ],
            '文字が false の時、FalseToken のインスタンスを返す' => [
                'false',
                FalseToken::class,
            ],
            '文字が null の時、NullToken のインスタンスを返す' => [
                'null',
                NullToken::class,
            ],
        ];
    }
}
