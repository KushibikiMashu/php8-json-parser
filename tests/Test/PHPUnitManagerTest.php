<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser\Test\Test;

use Panda\ToyJsonParser\Test\PHPUnitManager;

final class PHPUnitManagerTest extends \PHPUnit\Framework\TestCase
{
    private PHPUnitManager $phpUnit;

    protected function setUp(): void
    {
        $this->phpUnit = new PHPUnitManager();
    }

    /**
     * @test
     */
    public function 一つのクラスを指定した時、そのクラスのテストだけ実行される()
    {
        // ValueParserTest のテスト数は5
        $classes = ['Panda\ToyJsonParser\Test\Parser\ValueParserTest'];
        $actual = $this->phpUnit->run($classes);
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

        $actual = $this->phpUnit->run($classes);
        $this->assertMatchesRegularExpression('/6 tests/', $actual);
    }
}
