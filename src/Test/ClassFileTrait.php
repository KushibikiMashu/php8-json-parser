<?php

namespace Panda\ToyJsonParser\Test;

trait ClassFileTrait
{
    public function isPhpFile(): bool
    {
        return str_contains($this->filename, '.php');
    }

    public function equals(FileInterface $that): bool
    {
        return $this->filename === $that->getFilename();
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
}
