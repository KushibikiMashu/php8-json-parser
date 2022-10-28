<?php

namespace Panda\ToyJsonParser\Test;

trait ClassFileTrait
{
    public function equals(FileInterface $that): bool
    {
        return $this->filename === $that->getFilename();
    }

    public function isPhpFile(): bool
    {
        // PHP8 なら str_ends_with が使える
        // https://www.php.net/manual/ja/function.str-ends-with.php
        return substr($this->filename, -4) === '.php';
    }

    public function excludeExtension(): string
    {
        return str_replace('.php', '', $this->filename);
    }

    public function rootDir(): string
    {
        $exploded = explode('/', $this->filename);
        return $exploded[0] . '/';
    }

    public function filenameWithoutRootDir(): string
    {
        return str_replace($this->rootDir(), '', $this->excludeExtension());
    }

    public function __toString(): string
    {
        return $this->filename;
    }
}
