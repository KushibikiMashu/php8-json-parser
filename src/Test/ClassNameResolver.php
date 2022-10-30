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
     * @return string 引数のファイルのクラスの絶対クラス名
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

    /**
     * @param TestClassFile[] $files
     * @return string[] 引数のファイルのクラスの絶対クラス名の配列
     */
    public function resolveAbsoluteClassNameList(array $files): array
    {
        $list = [];
        foreach ($files as $file) {
            $absoluteTestClassName = $this->resolveAbsoluteClassName($file);
            // 重複を排除する
            $list[$absoluteTestClassName] = 1;
        }

        return array_keys($list);
    }
}
