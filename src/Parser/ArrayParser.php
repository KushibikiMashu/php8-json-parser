<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Parser;

use Panda\ToyJsonParser\Lexer\Lexer;
use Panda\ToyJsonParser\Lexer\Token\CommaToken;
use Panda\ToyJsonParser\Lexer\Token\EofToken;
use Panda\ToyJsonParser\Lexer\Token\RightSquareBracketToken;

final class ArrayParser
{
    const STATE_START = 'start';
    const STATE_VALUE = 'value';
    const STATE_COMMA = 'comma';

    public static function parse(Lexer $lexer): array
    {
        $array = [];
        $state = self::STATE_START;

        while (true) {
            $token = $lexer->getNextToken();
            if ($token instanceof EofToken) {
                break;
            }

            switch ($state) {
                case self::STATE_START:
                    if ($token instanceof RightSquareBracketToken){
                        return $array;
                    }
                    $array[] = ValueParser::parse($lexer, $token);
                    $state = self::STATE_VALUE;
                    break;
                case self::STATE_VALUE:
                    if ($token instanceof RightSquareBracketToken){
                        return $array;
                    }
                    if ($token instanceof CommaToken) {
                        $state = self::STATE_COMMA;
                        break;
                    }
                    throw new ParserException(token: $token);
                case self::STATE_COMMA:
                    $array[] = ValueParser::parse($lexer, $token);
                    $state = self::STATE_VALUE;
                    break;
            }
        }

        throw new ParserException(message: 'No end of array');
    }
}
