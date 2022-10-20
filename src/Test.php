<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser;

use Error;

final class Test
{
    public function main(): string
    {
        $files = $this->getChangedFilesAtLastCommit();

        if (!$this->existsPhpFile($files)) {
            return 'No tests.';
        }

        $classMap = [];
        $config = $this->getNamespaceMap();

        foreach ($files as $file) {
            if (str_starts_with($file, 'tests')) {
                $className = $this->findTestAbsoluteClassName($config, $file);
            } else {
                $testFile = $this->findTestFileByProdFilePath($file);
                $className = $this->findTestAbsoluteClassName($config, $testFile);
            }

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

    public function getChangedFilesAtLastCommit(): array
    {
        exec('git log --name-only --oneline e5779a5^..e5779a5', $output);

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
}
