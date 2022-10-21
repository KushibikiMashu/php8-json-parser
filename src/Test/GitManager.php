<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test;

final class GitManager
{
    public function getCurrentBranch(): string
    {
        exec('git branch --contains', $output);
        $line = $output[0];
        // "* branch-name"の形式で値が返ってくるので、ブランチ名だけを取り出している
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

//exec('git branch --contains', $output);
//exec("git log --no-merges --name-only --oneline $source..$end", $output);
