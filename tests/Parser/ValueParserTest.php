<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test\Parser;

use Panda\ToyJsonParser\Lexer\Lexer;
use Panda\ToyJsonParser\Lexer\Token\{
    FalseToken,
    NullToken,
    NumberToken,
    StringToken,
    TokenInterface,
    TrueToken,
};
use Panda\ToyJsonParser\Parser\ValueParser;
use PHPUnit\Framework\TestCase;

final class ValueParserTest extends TestCase
{
    /**
     * @test
     * @dataProvider dataProvider
     */
    public function parse(TokenInterface $input, $expected)
    {
        $lexer = new Lexer('');
        $result = ValueParser::parse($lexer, $input);
        $this->assertEquals($expected, $result);
    }

    public function dataProvider()
    {
        return [
            'token が TrueToken の時、true を返す' => [
                'input' => new TrueToken(),
                'expected' => true,
            ],
            'token が FalseToken の時、false を返す' => [
                'input' => new FalseToken(),
                'expected' => false,
            ],
            'token が NullToken の時、null を返す' => [
                'input' => new NullToken(),
                'expected' => null,
            ],
            'token が文字列 test の StringToken の時、test を返す' => [
                'input' => new StringToken('test'),
                'expected' => 'test',
            ],
            'token が NumberToken("0") の時、0 を返す' => [
                'input' => new NumberToken(0),
                'expected' => 0,
            ],
        ];
    }
}
