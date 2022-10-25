<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test;

final class Finder
{
    private string $rootDir = __DIR__  . '/../../';

    public function exists(FileInterface $file): bool
    {
        // project の root dir にまで戻る必要がある
        return file_exists($this->rootDir . $file->getFilename());
    }

    public function findTestFileByProdFilePath(ClassFile $file): TestClassFile
    {
        $exploded = explode('/', $file->getFilename());
        $replaced = str_replace($exploded[0], 'tests', $file->getFilename());
        $filename = str_replace('.php', 'Test.php', $replaced);
        return new TestClassFile($filename);
    }
}
