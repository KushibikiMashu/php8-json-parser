<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test;

final class FileFactory
{
    // OtherFile、ClassFile、 TestClassFile の3種類のいずれかを作る
    public function create(string $filename): FileInterface
    {
        $extension = pathinfo($filename)['extension'];
        if ($extension !== 'php') {
            return new OtherFile($filename);
        }

        preg_match('/\/.+Test\.php/u', $filename, $matches);
        return count($matches) > 0 ? new TestClassFile($filename) : new ClassFile($filename);
    }
}
