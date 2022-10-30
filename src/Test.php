<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser;

use Panda\ToyJsonParser\Test\Branch;
use Panda\ToyJsonParser\Test\ClassNameResolver;
use Panda\ToyJsonParser\Test\FileArrayUtils;
use Panda\ToyJsonParser\Test\FileFactory;
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
        $mainBranch = new Branch('main');
        $finder = new Finder();
        $utils = new FileArrayUtils();

        $currentBranch = $this->git->getCurrentBranch();
        $filenames = $this->git->getAllChangedFiles($currentBranch->getName(), $mainBranch->getName(), $toHash);
        $files = array_map(fn ($filename) => (new FileFactory())->create($filename), $filenames);
        $phpFiles = $utils->filterPhpFiles($files);
        [$classFiles, $testFiles] = $utils->separateFiles($phpFiles);
        $AllDependedFiles = $finder->findAllDependedFiles($classFiles);
        $dependedTestFiles = $utils->filterTestFiles($AllDependedFiles);
        $allTestFiles = $utils->concatFiles($testFiles, $dependedTestFiles);
        $classList = $this->createAbsoluteClassNameList($allTestFiles);

        if (count($classList) === 0) {
            return 'No tests.';
        }

        return $this->phpUnit->run($classList);
    }

    /**
     * @param TestClassFile[] $files
     * @return string[]
     */
    // TODO: ClassList か、他の名前のクラスに処理を切り出す
    public function createAbsoluteClassNameList(array $files): array
    {
        // constructor に入れる
        $resolver = new ClassNameResolver();

        $classList = [];
        foreach ($files as $file) {
            $absoluteTestClassName = $resolver->resolveAbsoluteClassName($file);

//            echo "executed: " . $absoluteClassName . PHP_EOL;

            $classList[$absoluteTestClassName] = 1;
        }

        return array_keys($classList);
    }
}

//echo (new Test())->main();

// クラス候補
// Commit

// ClassMap
// Config（ターゲットのブランチ名、コミットハッシュ、）
// Display
