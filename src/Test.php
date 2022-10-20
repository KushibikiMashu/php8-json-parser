<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser;

use Error;

final class Test
{
    public function main(string $toHash = null): string
    {
        $currentBranch = $this->getCurrentBranchName();
        $files = $this->getAllChangedFiles($currentBranch, 'main', $toHash);

        if (!$this->existsPhpFile($files)) {
            return 'No tests.';
        }

        $classMap = [];
        $config = $this->getNamespaceMap();
        foreach ($files as $file) {
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

            echo "executed: " . $className . PHP_EOL;

            $classMap[$className] = 1;
        }

        return $this->runTests(array_keys($classMap));
    }

    private function existsPhpFile(array $files): bool
    {
        foreach ($files as $file) {
            if (str_contains($file, '.php')) {
                return true;
            }
        }

        return false;
    }

    private function getNamespaceMap(): array
    {
        $json = file_get_contents(__DIR__ . '/../composer.json');
        $config = json_decode($json, true);

        return array_flip($config['autoload']['psr-4']);
    }

    public function runTests(array $classes): string
    {
        if (count($classes) === 0) {
            return 'No test executed.';
        }

        $className = $this->join($classes);
        $className = $this->format($className);

        exec("./Vendor/phpunit/phpunit/phpunit tests --filter '($className)'", $output);

        return implode(PHP_EOL, $output);
    }

    private function join(array $classes): string
    {
        return implode('|', $classes);
    }

    private function format(string $className): string
    {
        return str_replace('\\', '\\\\', $className);
    }

    public function getChangedFilesFromCommitHashes(string $from, ?string $to = null): array
    {
        $hashes = $to ? "$from^..$to" : "$from^..$from";
        exec("git log --name-only --oneline $hashes", $output);

        return array_slice($output, 1);
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

    public function diffHashesFromTargetBranch(string $target, string $source = 'main', string $to = null): array
    {
        $end = $to ?? $target;
        exec("git log --no-merges --oneline $source..$end", $output);
        $hashes = [];

        foreach ($output as $line) {
            $hashes[] = explode(' ', $line)[0];
        }

        return $hashes;
    }

    public function getCurrentBranchName(string $current = null): string
    {
        if ($current) {
            return $current;
        }

        exec('git branch --contains', $output);
        $line = $output[0];
        return str_replace('* ', '', $line);
    }

    public function getAllChangedFiles(string $target, string $source = 'main', string $to = null): array
    {
        $end = $to ?? $target;
        exec("git log --no-merges --name-only --oneline $source..$end", $output);
        $files = [];

        foreach ($output as $line) {
            if (!str_contains($line, '.php')) {
                continue;
            }
            $files[$line] = 1;
        }

        return array_keys($files);
    }
}

echo (new Test())->main();

// クラス候補
// GitManager
// PHPUnitManager
// Commit
// Branch
// File
// TestFile
// ClassMap
// TestRunner
// Config（ターゲットのブランチ名、コミットハッシュ、）
// Display
