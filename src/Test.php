<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser;

use Error;
use Panda\ToyJsonParser\Test\GitManager;
use Panda\ToyJsonParser\Test\PHPUnitManager;

final class Test
{
    private GitManager $git;
    private PHPUnitManager $phpUnit;

    public function __construct()
    {
        $this->git = new GitManager();
        $this->phpUnit = new PHPUnitManager();
    }

    public function main(string $toHash = null): string
    {
        $currentBranch = $this->git->getCurrentBranch();
        $files = $this->git->getAllChangedFiles($currentBranch->getName(), 'main', $toHash);
        $phpFiles = $this->filterPhpFile($files);
        if (count($phpFiles) === 0) {
            return 'No tests.';
        }

        $classMap = [];
        $config = $this->getNamespaceMap();
        foreach ($phpFiles as $file) {
            $existsFile = file_exists(__DIR__ . '/../' . $file);
            if (!$existsFile) {
                continue;
            }

            // このクラスのテストを実行すると再帰的に実行されてしまうので、スキップする
            if ($file === 'tests/TestTest.php' || $file === 'src/Test.php') {
                continue;
            }

            if (str_starts_with($file, 'tests')) {
                $className = $this->findTestAbsoluteClassName($config, $file);
            } else {
                $testFile = $this->findTestFileByProdFilePath($file);
                $className = $this->findTestAbsoluteClassName($config, $testFile);
            }

//            echo "executed: " . $className . PHP_EOL;

            $classMap[$className] = 1;
        }

        return $this->phpUnit->run(array_keys($classMap));
    }

    private function filterPhpFile(array $files): array
    {
        $phpFiles = [];
        foreach ($files as $file) {
            if (str_contains($file, '.php')) {
                $phpFiles[] = $file;
            }
        }

        return $phpFiles;
    }

    private function getNamespaceMap(): array
    {
        $json = file_get_contents(__DIR__ . '/../composer.json');
        $config = json_decode($json, true);

        return array_flip($config['autoload']['psr-4']);
    }

    public function findTestFileByProdFilePath(string $filepath): string
    {
        $exploded = explode('/', $filepath);
        $replaced = str_replace($exploded[0], 'tests', $filepath);
        return str_replace('.php', 'Test.php', $replaced);
    }

    public function findTestAbsoluteClassName(array $map, string $filepath): string
    {
        // phpunit.xml から取得する方がいいかも？
        $exploded = explode('/', $filepath);
        $testDir = $exploded[0] . '/';

        if (!isset($map[$testDir])) {
            throw new Error();
        }

        $withoutExtension = str_replace('.php', '', $filepath);
        $replaced = str_replace($testDir, $map[$testDir], $withoutExtension);

        return str_replace('/', '\\', $replaced);
    }
}

//echo (new Test())->main();

// クラス候補
// Commit
// Branch
// File
// Finder
// TestFile
// ClassMap
// TestRunner
// Config（ターゲットのブランチ名、コミットハッシュ、）
// Display
