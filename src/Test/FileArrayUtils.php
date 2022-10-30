<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test;

final class FileArrayUtils
{
    private Finder $finder;

    public function __construct()
    {
        $this->finder = new Finder();
    }

    /**
     * @param FileInterface[] $files
     * @return (ClassFile|TestClassFile)[] $files
     */
    public function filterPhpFiles(array $files): array
    {
        $thisFile = 'src/Test.php';
        $thisTestFile = 'tests/TestTest.php';

        $filtered = array_filter($files, function ($file) use ($thisFile, $thisTestFile) {
            if (!$this->finder->exists($file) || !$file->isPhpFile()) {
                return false;
            }

            // このクラスのテストを実行すると再帰的にテストが実行されて終わらないので、スキップする
            $filename = $file->getFilename();
            if ($filename === $thisFile || $filename === $thisTestFile) {
                return false;
            }

            return true;
        });
        // array_filter では key が固定されたままなので、配列を作り直すことで key をリセットする
        return [...$filtered];
    }

    /**
     * @param FileInterface[] $files
     * @return (ClassFile|TestClassFile)[] $files
     */
    public function filterTestFiles(array $files): array
    {
        $filtered = array_filter($files, function ($file) {
            return $this->finder->exists($file) && $file->isTestFile();
        });
        // array_filter では key が固定されたままなので、配列を作り直すことで key をリセットする
        return [...$filtered];
    }

    /**
     * @param (ClassFile|TestClassFile)[] $files
     * @return array{0: ClassFile[], 1: TestClassFile[] }
     */
    public function separateFiles(array $files): array
    {
        $classFiles = [];
        $testFiles = [];

        foreach ($files as $file) {
            if ($file->isTestFile()) {
                $testFiles[] = $file;
            } else {
                $classFiles[] = $file;
            }
        }

        return [$classFiles, $testFiles];
    }

    public function concatFiles(array $filesA, array $filesB): array
    {
        $diffs = array_diff($filesB, $filesA);
        return [...$filesA, ...$diffs];
    }
}
