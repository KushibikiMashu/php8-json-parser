<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser;

use Panda\ToyJsonParser\Test\Branch;
use Panda\ToyJsonParser\Test\ClassFile;
use Panda\ToyJsonParser\Test\ClassNameResolver;
use Panda\ToyJsonParser\Test\Finder;
use Panda\ToyJsonParser\Test\GitManager;
use Panda\ToyJsonParser\Test\PHPUnitManager;
use Panda\ToyJsonParser\Test\TestClassFile;

final class Test
{
    private GitManager $git;
    private PHPUnitManager $phpUnit;

    public function __construct()
    {
        $this->git = new GitManager();
        $this->phpUnit = new PHPUnitManager();
    }

    public function main(string $toHash = null): string
    {
        $finder = new Finder();
        $mainBranch = new Branch('main');
        $resolver = new ClassNameResolver();

        $currentBranch = $this->git->getCurrentBranch();
        $files = $this->git->getAllChangedFiles($currentBranch->getName(), $mainBranch->getName(), $toHash);

        $classMap = [];
        foreach ($files as $file) {
            if (!$finder->exists($file) || !$file->isPhpFile()) {
                continue;
            }

            // このクラスのテストを実行すると再帰的にテストが実行されて終わらないので、スキップする
            $filename = $file->getFilename();
            if ($filename === 'tests/TestTest.php' || $filename === 'src/Test.php') {
                continue;
            }

            if ($file->isTestFile()) {
                /* @var TestClassFile $file  */
                $className = $resolver->findTestAbsoluteClassName($file);
            } else {
                /* @var ClassFile $file  */
                $testFile = $finder->findTestFileByProdFilePath($file);
                $className = $resolver->findTestAbsoluteClassName($testFile);
            }

//            echo "executed: " . $className . PHP_EOL;

            $classMap[$className] = 1;
        }

        if (count($classMap) === 0) {
            return 'No tests.';
        }

        return $this->phpUnit->run(array_keys($classMap));
    }
}

//echo (new Test())->main();

// クラス候補
// Commit

// ClassMap
// Config（ターゲットのブランチ名、コミットハッシュ、）
// Display
