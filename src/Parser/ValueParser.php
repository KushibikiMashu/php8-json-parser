<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Parser;

use Panda\ToyJsonParser\Lexer\Lexer;
use Panda\ToyJsonParser\Lexer\Token\FalseToken;
use Panda\ToyJsonParser\Lexer\Token\LeftCurlyBracketToken;
use Panda\ToyJsonParser\Lexer\Token\LeftSquareBracketToken;
use Panda\ToyJsonParser\Lexer\Token\NullToken;
use Panda\ToyJsonParser\Lexer\Token\NumberToken;
use Panda\ToyJsonParser\Lexer\Token\StringToken;
use Panda\ToyJsonParser\Lexer\Token\TokenInterface;
use Panda\ToyJsonParser\Lexer\Token\TrueToken;

final class ValueParser
{
    /**
     * @param Lexer $lexer
     * @param TokenInterface $token
     * @return array|string|int|bool|null
     * @throws ParserException
     */
    public static function parse(Lexer $lexer, TokenInterface $token): array|string|int|bool|null {
        return match(true) {
            $token instanceof TrueToken => true,
            $token instanceof FalseToken => false,
            $token instanceof NullToken => null,
            $token instanceof StringToken => $token->getValue(),
            $token instanceof NumberToken => $token->getValue(),
            $token instanceof LeftSquareBracketToken => ArrayParser::parse($lexer),
            $token instanceof LeftCurlyBracketToken => ObjectParser::parse($lexer),
            default => throw new ParserException(token: $token),
        };
    }
}
