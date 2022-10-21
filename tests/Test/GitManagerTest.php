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
    public function 複数コミットから複数ファイルを取得する()
    {
        $actual = $this->git->getAllChangedFiles('unit-test', 'main', 'd3fb679');

        $this->assertSame(['src/Test.php', 'src/TestTest.php'], $actual);
    }
}
