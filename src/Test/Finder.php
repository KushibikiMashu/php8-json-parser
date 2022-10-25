<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test;

final class Finder
{
    // TODO: 設定ファイルから root ディレクトリを取得するようにする
    private string $rootDir = __DIR__ . '/../../';

    private GitManager $git;
    private FileFactory $factory;

    public function __construct()
    {
        $this->git = new GitManager();
        $this->factory = new FileFactory();
    }

    public function exists(FileInterface $file): bool
    {
        return file_exists($this->rootDir . $file->getFilename());
    }

    public function findTestFileByProdFilePath(ClassFile $file): TestClassFile
    {
        $exploded = explode('/', $file->getFilename());
        $replaced = str_replace($exploded[0], 'tests', $file->getFilename());
        $filename = str_replace('.php', 'Test.php', $replaced);
        return new TestClassFile($filename);
    }

    /**
     * @param ClassFile|TestClassFile $file
     * @return FileInterface[]
     */
    // FIXME: PHP8 なら ClassFile|TestClassFile で引数の型をつけられる
    public function findUsingClasses($file): array
    {
        $className = pathinfo($file->getFilename())['filename'];
        // TODO: namespace を渡す
        $filenames = $this->git->grepUsingFilenames($className);

        return array_map(fn ($filename) => $this->factory->create($filename), $filenames);
    }
}
