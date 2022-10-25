<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test;

interface FileInterface
{
    public function isTestFile(): bool;

    public function isPhpFile(): bool;
}
