<?php
declare(strict_types=1);

namespace Panda\ToyJsonParser\Test\Test;

use Panda\ToyJsonParser\Test\ClassFile;
use Panda\ToyJsonParser\Test\FileArrayUtils;
use Panda\ToyJsonParser\Test\OtherFile;
use Panda\ToyJsonParser\Test\TestClassFile;
use PHPUnit\Framework\TestCase;

class FileArrayUtilsTest extends TestCase
{
    private FileArrayUtils $utils;

    protected function setUp(): void
    {
        $this->utils = new FileArrayUtils();
    }

    /**
     * @test
     */
    public function ファイルの配列が渡されたとき、PHPのファイルだけを返す()
    {
        $files = [
            new ClassFile('src/Parser/ValueParser.php'),
            new TestClassFile('tests/Parser/ObjectParserTest.php'),
            new OtherFile('composer.json'),
        ];
        $actual = $this->utils->filterPhpFiles($files);
        $this->assertEquals([
            new ClassFile('src/Parser/ValueParser.php'),
            new TestClassFile('tests/Parser/ObjectParserTest.php'),
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
        $actual = $this->utils->filterTestFiles($files);
        $this->assertEquals([
            new TestClassFile('tests/Parser/ObjectParserTest.php'),
        ], $actual);
    }

    /**
     * @test
     */
    public function 配列を2つ渡された時、要素をuniqueにした配列を返す()
    {
        $filesA = [
            new ClassFile('src/Parser/ValueParser.php'),
            new TestClassFile('tests/Parser/ObjectParserTest.php'),
        ];
        $filesB = [
            new ClassFile('src/Parser/ValueParser.php'),
            new OtherFile('composer.json'),
        ];
        $actual = $this->utils->concatFiles($filesA, $filesB);
        $this->assertEquals([
            new ClassFile('src/Parser/ValueParser.php'),
            new TestClassFile('tests/Parser/ObjectParserTest.php'),
            new OtherFile('composer.json'),
        ], $actual);
    }

    /**
     * @test
     */
    public function 実装ファイルとテストファイルの配列が渡されたとき、ファイルを分けて返す()
    {
        $files = [
            new ClassFile('src/Parser/ValueParser.php'),
            new TestClassFile('tests/Parser/ObjectParserTest.php'),
        ];
        $actual = $this->utils->separateFiles($files);
        $this->assertEquals([
            [new ClassFile('src/Parser/ValueParser.php')],
            [new TestClassFile('tests/Parser/ObjectParserTest.php')],
        ], $actual);
    }
}
