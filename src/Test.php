<?php

declare(strict_types=1);

namespace Panda\ToyJsonParser;

use Panda\ToyJsonParser\Test\Branch;
use Panda\ToyJsonParser\Test\ClassFile;
use Panda\ToyJsonParser\Test\ClassNameResolver;
use Panda\ToyJsonParser\Test\FileFactory;
use Panda\ToyJsonParser\Test\FileInterface;
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

        $currentBranch = $this->git->getCurrentBranch();
        $filenames = $this->git->getAllChangedFiles($currentBranch->getName(), $mainBranch->getName(), $toHash);
        $files = array_map(fn ($filename) => (new FileFactory())->create($filename), $filenames);
        $phpFiles = $this->filterPhpFiles($files);
        [$classFiles, $testFiles] = $this->separateFiles($phpFiles);
        $AllDependedFiles = $finder->findAllDependedFiles($classFiles);
        $dependedTestFiles = $this->filterTestFiles($AllDependedFiles);
        $allTestFiles = $this->concatFiles($testFiles, $dependedTestFiles);
        $classList = $this->createAbsoluteClassNameList($allTestFiles);

        if (count($classList) === 0) {
            return 'No tests.';
        }

        return $this->phpUnit->run($classList);
    }

    /**
     * @param (ClassFile|TestClassFile)[] $files
     * @return array{0: ClassFile[], 1: TestClassFile[] }
     */
    public function separateFiles(array $files): array
    {
        $classFiles = [];
        $testFiles = [];

        foreach ($files as $file) {
            if ($file->isTestFile()) {
                $testFiles[] = $file;
            } else {
                $classFiles[] = $file;
            }
        }

        return [$classFiles, $testFiles];
    }

    /**
     * @param FileInterface[] $files
     * @return (ClassFile|TestClassFile)[] $files
     */
    public function filterPhpFiles(array $files): array
    {
        $finder = new Finder();
        $thisFile = 'src/Test.php';
        $thisTestFile = 'tests/TestTest.php';

        $filtered = array_filter($files, function ($file) use ($finder, $thisFile, $thisTestFile) {
            if (!$finder->exists($file) || !$file->isPhpFile()) {
                return false;
            }

            // このクラスのテストを実行すると再帰的にテストが実行されて終わらないので、スキップする
            $filename = $file->getFilename();
            if ($filename === $thisFile || $filename === $thisTestFile) {
                return false;
            }

            return true;
        });
        // array_filter では key が固定されたままなので、配列を作り直すことで key をリセットする
        return [...$filtered];
    }

    /**
     * @param FileInterface[] $files
     * @return (ClassFile|TestClassFile)[] $files
     */
    public function filterTestFiles(array $files): array
    {
        $finder = new Finder();
        $filtered = array_filter($files, function ($file) use ($finder) {
            return $finder->exists($file) && $file->isTestFile();
        });
        // array_filter では key が固定されたままなので、配列を作り直すことで key をリセットする
        return [...$filtered];
    }

    public function concatFiles(array $filesA, array $filesB): array
    {
        $diffs = array_diff($filesB, $filesA);
        return [...$filesA, ...$diffs];
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
