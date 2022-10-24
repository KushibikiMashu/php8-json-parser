<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test;

final class GitManager
{
    public function getCurrentBranch(): Branch
    {
        exec('git branch --contains', $output);
        $line = $output[0];
        // "* branch-name"の形式で値が返ってくるので、ブランチ名だけを取り出している
        $name = str_replace('* ', '', $line);
        return new Branch($name);
    }

    /**
     * @return FileInterface[]
     */
    public function getAllChangedFiles(string $target, string $source = 'main', string $to = null): array
    {
        $end = $to ?? $target;
        exec("git log --no-merges --name-only --oneline $source..$end", $output);

        $files = [];
        foreach ($output as $line) {
            // コミットの行をスキップする。コミットの識別の仕方が面倒なので、スペースの有無で判断する
            // ファイル名にはスペースがないため
            if (str_contains($line, ' ')) {
                continue;
            }
            $files[$line] = 1;
        }

        return array_map(function ($filename) {
            return (new FileFactory())->create($filename);
        }, array_keys($files));
    }
}
