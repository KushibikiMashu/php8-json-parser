<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test;

final class Finder
{
    // TODO: 設定ファイルから root ディレクトリを取得するようにする
    private string $rootDir = __DIR__ . '/../../';

    private array $dependentFiles;

    private GitManager $git;
    private FileFactory $factory;
    private ClassNameResolver $resolver;

    public function __construct()
    {
        $this->git = new GitManager();
        $this->factory = new FileFactory();
        $this->resolver = new ClassNameResolver();
        $this->dependentFiles = [];
    }

    public function exists(FileInterface $file): bool
    {
        return file_exists($this->rootDir . $file->getFilename());
    }

    // TODO: 文字列を操作しているだけなので別クラスに切り出す
    // TODO: ファイルがない場合 null を返す
    public function findTestFileByClassFile(ClassFile $file): TestClassFile
    {
        // TODO: file_exists する。なければ false を返す？
        $exploded = explode('/', $file->getFilename());
        $replaced = str_replace($exploded[0], 'tests', $file->getFilename());
        $filename = str_replace('.php', 'Test.php', $replaced);
        return new TestClassFile($filename);
    }

    /**
     * 直接的に依存されているファイルを返す
     *
     * @return (ClassFile|TestClassFile)[]
     */
    public function findDirectlyDependedFiles(ClassFile $file): array
    {
        $absoluteClassName = $this->resolver->resolveAbsoluteClassName($file);
        $filenames = $this->git->grepDependedClassFilenames($absoluteClassName);
        return array_map(fn ($filename) => $this->factory->create($filename), $filenames);
    }

    /**
     * findAllDependedFiles のラッパー
     *
     * @param ClassFile[] $files
     * @return (ClassFile|TestClassFile)[]
     */
    public function findAllDependedFiles(array $files): array
    {
//         TODO: implements
    }

    /**
     * 直接的・間接的に依存されているファイルを返す
     *
     * @param ClassFile $file
     * @return (ClassFile|TestClassFile)[]
     */
    public function findDependedFiles(ClassFile $file): array
    {
        $this->dependentFiles[$file->getFilename()] = 1;

        var_dump('');
        var_dump('------- rec start');

        $unique = array_unique($this->findRecursivelyDependedFile($file));
        sort($unique);
        return array_map(fn ($filename) => $this->factory->create($filename), $unique);;
    }

    private function findRecursivelyDependedFile(ClassFile $file): array
    {
        $files = $this->findDirectlyDependedFiles($file);
        if (count($files) === 0) {
            return [];
        }

        foreach ($files as $file) {
            $filename = $file->getFilename();
            var_dump('targ | ' . $filename);
            if (isset($this->dependentFiles[$filename])) {
                var_dump('skip | ' . $filename);
                continue;
            }
            $this->dependentFiles[$filename] = 1;

            // TestFile は終端なので何にも依存されていない
            // TestFile の git grep をスキップすることで、grep 回数を減らして全体の実行速度を上げる
            if ($file->isTestFile()) {
                var_dump('skip | ' . $filename);
                continue;
            }
            var_dump('chec | ' . $filename);
            var_dump('------- child rec start');
            $this->findRecursivelyDependedFile($file);
            var_dump('------- child rec end');
        }

        return array_keys($this->dependentFiles);
    }
}
