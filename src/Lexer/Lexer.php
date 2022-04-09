<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Lexer;

use Panda\ToyJsonParser\Lexer\Exception\LexerException;
use Panda\ToyJsonParser\Lexer\Token\ColonToken;
use Panda\ToyJsonParser\Lexer\Token\CommaToken;
use Panda\ToyJsonParser\Lexer\Token\EofToken;
use Panda\ToyJsonParser\Lexer\Token\FalseToken;
use Panda\ToyJsonParser\Lexer\Token\LeftCurlyBracketToken;
use Panda\ToyJsonParser\Lexer\Token\LeftSquareBracketToken;
use Panda\ToyJsonParser\Lexer\Token\NullToken;
use Panda\ToyJsonParser\Lexer\Token\NumberToken;
use Panda\ToyJsonParser\Lexer\Token\RightCurlyBracketToken;
use Panda\ToyJsonParser\Lexer\Token\RightSquareBracketToken;
use Panda\ToyJsonParser\Lexer\Token\StringToken;
use Panda\ToyJsonParser\Lexer\Token\TokenInterface;
use Panda\ToyJsonParser\Lexer\Token\TrueToken;

final class Lexer
{
    private int $length;
    private int $position;

    public function __construct(private string $json)
    {
        $this->length = mb_strlen($this->json);
        $this->position = 0;
    }

    public function current(): string
    {
        return mb_substr($this->json, $this->position, 1);
    }

    public function getNextToken(): TokenInterface
    {
        do {
            $ch = $this->consume();
            if ($ch === null) {
                return new EofToken();
            }
        } while ($this->isSkipCharacter($ch));

        return match ($ch) {
            '[' => new LeftSquareBracketToken(),
            ']' => new RightSquareBracketToken(),
            '{' => new LeftCurlyBracketToken(),
            '}' => new RightCurlyBracketToken(),
            ':' => new ColonToken(),
            ',' => new CommaToken(),
            '"' => $this->getStringToken(),
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' => $this->getNumberToken($ch),
            't' => $this->getLiteralToken('true', TrueToken::class),
            'f' => $this->getLiteralToken('false', FalseToken::class),
            'n' => $this->getLiteralToken('null', NullToken::class),
            default => throw new LexerException('Invalid character ' . $ch),
        };
    }

    private function consume(): ?string
    {
        if ($this->length <= $this->position) {
            return null;
        }

        $ch = $this->current();
        $this->position++;

        return $ch;
    }

    private function isSkipCharacter(?string $ch): bool
    {
        return $ch === ' ' || $ch === "\t" || $ch === "\n" || $ch === "\r";
    }

    private function getStringToken(): StringToken
    {
        $str = '';

        while (true) {
            $ch = $this->consume();

            if ($ch === null) {
                break;
            } else if ($ch === '"') {
                return new StringToken($str);
            } else if ($ch !== '\\') {
                $str .= $ch;
                continue;
            }

            $str .= match ($ch = $this->consume()) {
                '"' => '"',
                '\\' => '\\',
                '/' => '/',
                'b' => chr(0x8),
                'f' => "\f",
                'n' => "\n",
                'r' => "\r",
                't' => "\t",
                default => '\\' . $ch,
            };
        }

        throw new LexerException('No end of string');
    }

    private function getNumberToken(string $ch): NumberToken
    {
        $number = $ch;

        while (true) {
            $ch = $this->current();
            if ('0' <= $ch && $ch <= '9') {
                $number .= $ch;
                $this->consume();
                continue;
            }

            break;
        }

        return new NumberToken((int)$number);
    }

    private function getLiteralToken(string $expectedName, string $klass): TrueToken|FalseToken|NullToken
    {
        $name = $expectedName[0];

        for ($i = 1; $i < strlen($expectedName); $i++) {
            $ch = $this->consume();
            if ($ch === null) {
                throw new LexerException('Unexpected end of text');
            }

            $name .= $ch;
        }

        if ($name !== $expectedName) {
            throw new LexerException('Unexpected literal ' . $ch);
        }

        return new $klass;
    }
}
