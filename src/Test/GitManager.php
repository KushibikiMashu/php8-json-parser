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
     * @return string[]
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

        return array_unique(array_keys($files));
    }

    /**
     * 実装ファイルの絶対クラス名を渡す
     *
     * @return string[]
     */
    public function grepDependedClassFilenames(string $absoluteClassName): array
    {
        var_dump('grep | ' . $absoluteClassName);

        $exploded = explode('\\', $absoluteClassName);
        $className = $exploded[count($exploded) -1];
        // FIXME: 絶対クラス名での検索は文字列がひっかかってしまう。いい対策が思いつくまでコメントアウトする
//        $addedBackSlashes = str_replace('\\', '\\\\', $absoluteClassName);

        exec("git grep -l -e 'use' --and -w -e '$className;'", $used);
        // FIXME: Parser が xxxParser に引っかかる問題に対して半角スペースを追加することで対策をおこなっている
        // ただし、行頭の場合に対応できないので、根本的に解決を行う必要がある
        exec("git grep -l -e ' $className::' --or -e 'new $className('", $called);
//        exec("git grep -l -e '$addedBackSlashes'", $maybeCalled);

//        $sorted = [...$used, ...$called, ...$maybeCalled];
        $sorted = [...$used, ...$called];
        sort($sorted);

        var_dump('resu | ' . implode(', ', array_unique($sorted)));

        return array_unique($sorted);
    }
}
