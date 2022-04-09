<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Parser;

use Panda\ToyJsonParser\Lexer\Token\NumberToken;
use Panda\ToyJsonParser\Lexer\Token\StringToken;
use Panda\ToyJsonParser\Lexer\Token\TokenInterface;

final class ParserException extends \Exception
{
    public function __construct(TokenInterface $token = null, string $message = "Syntax error")
    {
        if ($token instanceof TokenInterface) {
            if ($token instanceof StringToken || $token instanceof NumberToken) {
                $message = sprintf("%s type=%s value=%s", $message, $token::class, $token->getValue());
            } else {
                $message = sprintf("%s type=%s", $message, $token::class);
            }
        }

        parent::__construct($message);
    }
}
