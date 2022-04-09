<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test\Parser;

use Panda\ToyJsonParser\Lexer\Lexer;
use Panda\ToyJsonParser\Parser\ObjectParser;
use PHPUnit\Framework\TestCase;

final class ObjectParserTest extends TestCase
{
    /**
     * @test
     */
    public function parse__jsonのオブジェクトをPHPの連想配列に変換する()
    {
        // { は ValueParser で消費済みのため、ObjectParser には以下の文字列が渡される
        $lexer = new Lexer('
            "foo": "bar",
            "number": 1,
            "true": true,
            "false": false,
            "null": null,
            "array": [1, 2, 3]
        }');
        $actual = ObjectParser::parse($lexer);
        $expected = [
            'foo' => 'bar',
            'number' => 1,
            'true' => true,
            'false' => false,
            'null' => null,
            'array' => [1, 2, 3]
        ];
        $this->assertEquals($expected, $actual);
    }
}
