<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser;

final class TestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function 一つのクラスを指定した時、そのクラスのテストだけ実行される()
    {
        // ValueParserTest のテスト数は5
        $classes = ['Panda\ToyJsonParser\Test\Parser\ValueParserTest'];
        $actual = (new Test())->runTests($classes);
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

        $actual = (new Test())->runTests($classes);
        $this->assertMatchesRegularExpression('/6 tests/', $actual);
    }

    /**
     * @test
     */
    public function 最後のコミットのファイル名を取得する()
    {
        $actual = (new Test())->getChangedFilesFromCommitHashes('e5779a5');
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
        $actual = (new Test())->findTestFileByProdFilePath('src/Parser/ObjectParser.php');
        $this->assertSame('tests/Parser/ObjectParserTest.php', $actual);
    }

    /**
     * @test
     */
    public function テストファイル名を渡したら、そのテストのnamespaceを取得する()
    {
        $actual = (new Test())->findTestAbsoluteClassName([
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
        $actual = (new Test())->main();
        $this->assertMatchesRegularExpression('/2 tests/', $actual);
    }

    /**
     * @test
     */
    public function mainブランチとの差分のコミットハッシュを全て取得する()
    {
        $actual = (new Test())->diffHashesFromTargetBranch('unit-test', 'main', '5c9396c');
        $this->assertSame(['5c9396c'], $actual);
    }
}
