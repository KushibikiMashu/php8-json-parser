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

final class Test
{
    private GitManager $git;
    private PHPUnitManager $phpUnit;
    private FileFactory $factory;

    public function __construct()
    {
        $this->git = new GitManager();
        $this->phpUnit = new PHPUnitManager();
        $this->factory = new FileFactory();
    }

    public function main(string $toHash = null): string
    {
        $mainBranch = new Branch('main');
        $finder = new Finder();
        $utils = new FileArrayUtils();
        $resolver = new ClassNameResolver();

        $currentBranch = $this->git->getCurrentBranch();
        $filenames = $this->git->getAllChangedFiles($currentBranch->getName(), $mainBranch->getName(), $toHash);
        $files = array_map(fn ($filename) => $this->factory->create($filename), $filenames);
        $phpFiles = $utils->filterPhpFiles($files);
        [$classFiles, $testFiles] = $utils->separateFiles($phpFiles);
        $AllDependedFiles = $finder->findAllDependedFiles($classFiles);
        $dependedTestFiles = $utils->filterTestFiles($AllDependedFiles);
        $allTestFiles = $utils->concatFiles($testFiles, $dependedTestFiles);
        $classList = $resolver->resolveAbsoluteClassNameList($allTestFiles);

        if (count($classList) === 0) {
            return 'No tests.';
        }

        return $this->phpUnit->run($classList);
    }
}

//echo (new Test())->main();

// クラス候補
// Commit

// ClassMap
// Config（ターゲットのブランチ名、コミットハッシュ、）
// Display
