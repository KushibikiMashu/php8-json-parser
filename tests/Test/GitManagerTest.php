<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test\Test;

use Panda\ToyJsonParser\Test\GitManager;

final class GitManagerTest extends \PHPUnit\Framework\TestCase
{
    private GitManager $git;

    protected function setUp(): void
    {
        $this->git = new GitManager();
    }

    /**
     * @test
     */
    public function 複数のコミットからそれぞれで変更があったファイルを配列で取得する()
    {
        $actual = $this->git->getAllChangedFiles('unit-test', 'main', 'd3fb679');

        $this->assertEquals(['src/Test.php', 'src/TestTest.php'], $actual);
    }

    /**
     * @test
     */
    public function 絶対クラス名を渡すと、そのクラスが使われているファイル名を配列で返す()
    {
        $actual = $this->git->grepDependedClassFilenames('Panda\ToyJsonParser\Parser\ValueParser');
        $this->assertEquals([
            'src/Parser/ArrayParser.php',
            'src/Parser/ObjectParser.php',
            'src/Parser/Parser.php',
            'tests/Parser/ValueParserTest.php',
        ], $actual);
    }
}

