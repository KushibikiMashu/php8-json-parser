<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test;

final class ClassFile implements FileInterface
{
    use ClassFileTrait;

    private string $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function isTestFile(): bool
    {
        return false;
    }
}
