<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test;

final class Finder
{
    // TODO: 設定ファイルから root ディレクトリを取得するようにする
    private string $rootDir = __DIR__ . '/../../';

    public function exists(FileInterface $file): bool
    {
        return file_exists($this->rootDir . $file->getFilename());
    }

    public function findTestFileByClassFile(ClassFile $file): TestClassFile
    {
        // TODO: file_exists する。なければ false を返す？
        $exploded = explode('/', $file->getFilename());
        $replaced = str_replace($exploded[0], 'tests', $file->getFilename());
        $filename = str_replace('.php', 'Test.php', $replaced);
        return new TestClassFile($filename);
    }
}
