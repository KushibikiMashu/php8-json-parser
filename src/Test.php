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
        $phpFiles = $finder->filterPhpFile($files);

        if (count($phpFiles) === 0) {
            return 'No tests.';
        }

        $classMap = [];
        foreach ($phpFiles as $file) {
            $filename = $file->getFilename();
            if (!$finder->exists($file)) {
                continue;
            }

            // このクラスのテストを実行すると再帰的に実行されてしまうので、スキップする
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

        return $this->phpUnit->run(array_keys($classMap));
    }
}

//echo (new Test())->main();

// クラス候補
// Commit

// ClassMap
// Config（ターゲットのブランチ名、コミットハッシュ、）
// Display
