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
            // PHP8 なら str_contains が使える
            if (strpos($line, ' ') !== false) {
                continue;
            }
            $files[$line] = 1;
        }

        // TODO: File 化するのは Finder の責務とする
        // Finder に切り出す or FileFactory を Test.php で呼び出す
        return array_map(function ($filename) {
            return (new FileFactory())->create($filename);
        }, array_keys($files));
    }

    /**
     * @return string[]
     */
    public function grepUsingFilenames(string $className): array
    {
        exec("git grep -l -e 'use' --and -w -e '$className'", $output);

        return $output;
    }
}
