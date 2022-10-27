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
    public function 変更の中にテストクラスがある場合は、それだけを実行する()
    {
        $actual = $this->test->main('2e7195f');
        $this->assertMatchesRegularExpression('/5 tests/', $actual);
    }

    /**
     * @group t
     * @test
     */
    public function 絶対クラス名を渡したら、直接的・間接的にそのクラスを使っている全てのクラスのファイルを配列で返す()
    {
        $actual = $this->test->findAllDependedFiles(new ClassFile('src/Parser/ValueParser.php'));
        // TODO: 返り値をクラス形式にする
        $this->assertEquals([
            'src/JsonParser.php',
            'src/Parser/ArrayParser.php',
            'src/Parser/ObjectParser.php',
            'src/Parser/Parser.php',
            'src/Parser/ValueParser.php',
            'tests/JsonParserTest.php',
            'tests/Parser/ArrayParserTest.php',
            'tests/Parser/ObjectParserTest.php',
            'tests/Parser/ValueParserTest.php',
        ], $actual);
    }

    /**
     * @test
     */
    public function ファイルを渡すと、そのテストクラスの絶対クラス名を配列で取得する()
    {
        $files = [
            new ClassFile('src/Parser/ValueParser.php'),
            new TestClassFile('tests/Parser/ObjectParserTest.php'),
        ];
        $actual = $this->test->createClassList($files);
        $this->assertSame([
            'Panda\ToyJsonParser\Test\Parser\ValueParserTest',
            'Panda\ToyJsonParser\Test\Parser\ObjectParserTest',
        ], $actual);
    }

    /**
     * @test
     */
    public function PHPファイルが変更されていない場合、空配列を返す()
    {
        $files = [new OtherFile('composer.json')];
        $actual = $this->test->createClassList($files);
        $this->assertSame([], $actual);
    }
}
