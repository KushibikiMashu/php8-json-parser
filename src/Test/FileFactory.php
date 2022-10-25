<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test;

final class FileFactory
{
    public function create(string $filename): FileInterface
    {
        if (!str_contains($filename, '.php')) {
            return new OtherFile($filename);
        }

        preg_match('/\/.+Test\.php/u', $filename, $matches);
        return count($matches) > 0 ? new TestClassFile($filename) : new ClassFile($filename);
    }
}
