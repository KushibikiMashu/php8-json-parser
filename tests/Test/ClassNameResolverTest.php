<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test\Test;

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
    public function テストファイル名を渡したら、そのテストのnamespaceを取得する()
    {
        $actual = $this->resolver->findTestAbsoluteClassName(new TestClassFile('tests/Parser/ObjectParserTest.php'));
        $this->assertSame('Panda\ToyJsonParser\Test\Parser\ObjectParserTest', $actual);
    }
}
