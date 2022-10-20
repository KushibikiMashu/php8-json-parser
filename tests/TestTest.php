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
    public function 一つのクラスを指定した時、そのクラスのテストだけ実行される()
    {
        // ValueParserTest のテスト数は5
        $classes = ['Panda\ToyJsonParser\Test\Parser\ValueParserTest'];
        $actual = $this->test->runTests($classes);
        $this->assertMatchesRegularExpression('/5 tests/', $actual);
    }

    /**
     * @test
     */
    public function 二つのクラスを指定した時、そのクラスのテストだけ実行される()
    {
        $classes = [
            'Panda\ToyJsonParser\Test\Parser\ValueParserTest',
            'Panda\ToyJsonParser\Test\Parser\ArrayParserTest',
        ];

        $actual = $this->test->runTests($classes);
        $this->assertMatchesRegularExpression('/6 tests/', $actual);
    }

    /**
     * @test
     */
    public function 最後のコミットのファイル名を取得する()
    {
        $actual = $this->test->getChangedFilesFromCommitHashes('e5779a5');
        $this->assertSame([
            'src/Parser/ObjectParser.php',
            'tests/Parser/ArrayParserTest.php',
            'tests/Parser/ObjectParserTest.php',
        ], $actual);
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

    /**
     * @test
     */
    public function mainブランチとの差分のコミットハッシュを全て取得する()
    {
        $actual = $this->test->diffHashesFromTargetBranch('unit-test', 'main', 'd3fb679');
        $this->assertSame(['d3fb679', '5c9396c'], $actual);
    }

    /**
     * @test
     */
    public function 複数コミットから複数ファイルを取得する()
    {
        $actual = $this->test->getAllChangedFiles('unit-test', 'main', 'd3fb679');

        $this->assertSame(['src/Test.php', 'src/TestTest.php'], $actual);
    }
}

//......array(3) {
//    [0]=>
//  string(27) "src/Parser/ObjectParser.php"
//    [1]=>
//  string(32) "tests/Parser/ArrayParserTest.php"
//    [2]=>
//  string(33) "tests/Parser/ObjectParserTest.php"
//}

//.....Farray(3) {
//[0]=>
//  string(18) "tests/TestTest.php"
//[1]=>
//  string(12) "src/Test.php"
//[2]=>
//  string(16) "src/TestTest.php"
//}
