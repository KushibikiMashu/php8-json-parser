<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test\Test;

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
    public function 実装ファイル名を渡したら、そのテストのファイル名を取得する()
    {
        $actual = $this->finder->findTestFileByProdFilePath(new ClassFile('src/Parser/ObjectParser.php'));
        $this->assertObjectEquals(new TestClassFile('tests/Parser/ObjectParserTest.php'), $actual);
    }

    /**
     * @test
     */
    public function 実装ファイルを渡すと、そのクラスが使われているファイルを配列で返す()
    {
        $class = new ClassFile('src/Test/ClassNameResolver.php');
        $actual = $this->finder->findUsingClasses($class);
        $this->assertEquals([
            new ClassFile('src/Test.php'),
            new TestClassFile('tests/Test/ClassNameResolverTest.php'),
        ], $actual);
    }
}
