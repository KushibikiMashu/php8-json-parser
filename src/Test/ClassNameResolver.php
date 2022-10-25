<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test;

use Error;

final class ClassNameResolver
{
    private array $classMap;

    public function __construct(array $classMap = null)
    {
        $this->classMap = $classMap ?? $this->init();
    }

    private function init(): array
    {
        $json = file_get_contents(__DIR__ . '/../../composer.json');
        $config = json_decode($json, true);

        return array_flip($config['autoload']['psr-4']);
    }

    /**
     * @param ClassFile|TestClassFile $file
     */
    public function resolveAbsoluteClassName($file): string
    {
        // phpunit.xml から取得する方がいいかも？
        $rootDir = $file->rootDir();
        if (!isset($this->classMap[$rootDir])) {
            throw new Error();
        }
        $replaced = str_replace('/', '\\', $file->filenameWithoutRootDir());

        return $this->classMap[$rootDir] . $replaced;
    }
}
