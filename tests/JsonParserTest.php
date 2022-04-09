<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test;

use Panda\ToyJsonParser\JsonParser;

final class JsonParserTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function test_parse()
    {
        $json = '{
                    "key1": 100,
                     "key2": "わ\"お",
                     "true":true,
                     "array":[123, 0],
                     "o": {
                        "a":1,
                        "b":[true,false,null]
                    }
                 }';
        $sut = new JsonParser();
        $expected = json_decode($json, associative: true);
        $this->assertSame($expected, $sut->parse($json));
    }
}
