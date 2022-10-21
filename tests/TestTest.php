<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test;

use Panda\ToyJsonParser\Test;

final class TestTest extends \PHPUnit\Framework\TestCase
{
    private Test $test;

    protected function setUp(): void
    {
        $this->test = new Test();
    }

    /**
     * @test
     */
    public function 実装ファイル名を渡したら、そのテストのファイル名を取得する()
    {
        $actual = $this->test->findTestFileByProdFilePath('src/Parser/ObjectParser.php');
        $this->assertSame('tests/Parser/ObjectParserTest.php', $actual);
    }

    /**
     * @test
     */
    public function テストファイル名を渡したら、そのテストのnamespaceを取得する()
    {
        $actual = $this->test->findTestAbsoluteClassName([
            "src/" => "Panda\ToyJsonParser\\",
            "tests/" => "Panda\ToyJsonParser\Test\\",
        ], 'tests/Parser/ObjectParserTest.php');
        $this->assertSame('Panda\ToyJsonParser\Test\Parser\ObjectParserTest', $actual);
    }

    /**
     * @test
     */
    public function 変更の中にテストクラスがある場合は、それだけを実行する()
    {
        $actual = $this->test->main('2e7195f');
        $this->assertMatchesRegularExpression('/5 tests/', $actual);
    }
}
