<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test\Test;

use Panda\ToyJsonParser\Test;
use Panda\ToyJsonParser\Test\Finder;
use Panda\ToyJsonParser\Test\ClassFile;
use Panda\ToyJsonParser\Test\TestClassFile;

final class FinderTest extends \PHPUnit\Framework\TestCase
{
    private Finder $finder;

    protected function setUp(): void
    {
        $this->finder = new Finder();
    }

    /**
     * @test
     */
    public function 実装ファイルを渡したら、そのテストのファイル名を取得する()
    {
        $actual = $this->finder->findTestFileByClassFile(new ClassFile('src/Parser/ObjectParser.php'));
        $this->assertObjectEquals(new TestClassFile('tests/Parser/ObjectParserTest.php'), $actual);
    }

    /**
     * @test
     */
    public function 絶対クラス名を渡したら、直接そのクラスを使っているのクラスのファイルを配列で返す()
    {
        $actual = $this->finder->findDependedFiles('Panda\ToyJsonParser\Parser\ValueParser');
        $this->assertEquals([
            new ClassFile('src/Parser/ArrayParser.php'),
            new ClassFile('src/Parser/ObjectParser.php'),
            new ClassFile('src/Parser/Parser.php'),
            new TestClassFile('tests/Parser/ValueParserTest.php'),
        ], $actual);
    }
}
