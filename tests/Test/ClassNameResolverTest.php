<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test\Test;

use Panda\ToyJsonParser\Test\ClassFile;
use Panda\ToyJsonParser\Test\ClassNameResolver;
use Panda\ToyJsonParser\Test\TestClassFile;

final class ClassNameResolverTest extends \PHPUnit\Framework\TestCase
{
    private ClassNameResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new ClassNameResolver(
            [
                "src/" => "Panda\ToyJsonParser\\",
                "tests/" => "Panda\ToyJsonParser\Test\\",
            ]
        );
    }

    /**
     * @test
     */
    public function 実装ファイル名を渡したら、そのクラスの絶対クラス名を取得する()
    {
        $actual = $this->resolver->resolveAbsoluteClassName(new ClassFile('src/Test/ClassNameResolver.php'));
        $this->assertSame('Panda\ToyJsonParser\Test\ClassNameResolver', $actual);
    }

    /**
     * @test
     */
    public function テストファイル名を渡したら、そのテストの絶対クラス名を取得する()
    {
        $actual = $this->resolver->resolveAbsoluteClassName(new TestClassFile('tests/Parser/ObjectParserTest.php'));
        $this->assertSame('Panda\ToyJsonParser\Test\Parser\ObjectParserTest', $actual);
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
        $actual = $this->resolver->resolveAbsoluteClassNameList($files);
        $this->assertSame([
            'Panda\ToyJsonParser\Parser\ValueParser',
            'Panda\ToyJsonParser\Test\Parser\ObjectParserTest',
        ], $actual);
    }
}
