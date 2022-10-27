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
     * @test
     */
    public function ファイルを渡すと、そのテストクラスの絶対クラス名を配列で取得する()
    {
        $files = [
            new ClassFile('src/Parser/ValueParser.php'),
            new TestClassFile('tests/Parser/ObjectParserTest.php'),
        ];
        $actual = $this->test->createAbsoluteClassNameList($files);
        $this->assertSame([
            'Panda\ToyJsonParser\Test\Parser\ValueParserTest',
            'Panda\ToyJsonParser\Test\Parser\ObjectParserTest',
        ], $actual);
    }

    /**
     * @test
     */
    public function ファイルの配列が渡されたとき、テストのファイルだけを返す()
    {
        $files = [
            new ClassFile('src/Parser/ValueParser.php'),
            new TestClassFile('tests/Parser/ObjectParserTest.php'),
            new OtherFile('composer.json'),
        ];
        $actual = $this->test->filterTestFiles($files);
        $this->assertEquals([
            new TestClassFile('tests/Parser/ObjectParserTest.php'),
        ], $actual);
    }
}
