<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Parser;

use Panda\ToyJsonParser\Lexer\Exception\LexerException as LexerExceptionAlias;
use Panda\ToyJsonParser\Lexer\Lexer;
use Panda\ToyJsonParser\Lexer\Token\ColonToken;
use Panda\ToyJsonParser\Lexer\Token\CommaToken;
use Panda\ToyJsonParser\Lexer\Token\EofToken;
use Panda\ToyJsonParser\Lexer\Token\RightCurlyBracketToken;
use Panda\ToyJsonParser\Lexer\Token\StringToken;

final class ObjectParser
{
    const STATE_START = 'start';
    const STATE_KEY = 'key';
    const STATE_COLON = 'colon';
    const STATE_VALUE = 'value';
    const STATE_COMMA = 'comma';

    /**
     * @param Lexer $lexer
     * @return array
     * @throws ParserException
     * @throws LexerExceptionAlias
     */
    public static function parse(Lexer $lexer): array
    {
        $array = [];
        $key = '';
        $state = self::STATE_START;

        while (true) {
            $token = $lexer->getNextToken();

            if ($token instanceof EofToken) {
                break;
            }

            switch ($state) {
                case self::STATE_START:
                    if ($token instanceof RightCurlyBracketToken){
                        return $array;
                    } else if ($token instanceof StringToken) {
                        $key = $token->getValue();
                        $state = self::STATE_KEY;
                        break;
                    }
                    throw new ParserException(token: $token);
                case self::STATE_KEY:
                    if ($token instanceof ColonToken) {
                        $state = self::STATE_COLON;
                        break;
                    }
                    throw new ParserException(token: $token);
                case self::STATE_COLON:
                    $array[$key] = ValueParser::parse($lexer, $token);
                    $state = self::STATE_VALUE;
                    break;
                case self::STATE_VALUE:
                    if ($token instanceof RightCurlyBracketToken) {
                        return $array;
                    } else if ($token instanceof CommaToken) {
                        $state = self::STATE_COMMA;
                        break;
                    }
                    throw new ParserException(token: $token);
                case self::STATE_COMMA:
                    if ($token instanceof StringToken) {
                        $key = $token->getValue();
                        $state = self::STATE_KEY;
                        break;
                    }
                    throw new ParserException(token: $token);
            }
        }

        throw new ParserException(message: 'No end of object');
    }
}
