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

        $currentBranch = $this->git->getCurrentBranch();
        $filenames = $this->git->getAllChangedFiles($currentBranch->getName(), $mainBranch->getName(), $toHash);
        $files = array_map(fn ($filename) => (new FileFactory())->create($filename), $filenames);

        $classList = $this->createClassList($files);

        if (count($classList) === 0) {
            return 'No tests.';
        }

        return $this->phpUnit->run($classList);
    }

    /**
     * @param FileInterface[] $files
     * @return string[]
     */
    // TODO: ClassList か、他の名前のクラスに処理を切り出す
    public function createClassList(array $files): array
    {
        // constructor に入れる
        $finder = new Finder();
        $resolver = new ClassNameResolver();

        $classList = [];
        foreach ($files as $file) {
            if (!$finder->exists($file) || !$file->isPhpFile()) {
                continue;
            }

            // このクラスのテストを実行すると再帰的にテストが実行されて終わらないので、スキップする
            $filename = $file->getFilename();
            if ($filename === 'tests/TestTest.php' || $filename === 'src/Test.php') {
                continue;
            }

            // TODO: 呼び出しているファイルを探して無限ループにならないように、
            // 一度調べたファイルは loop をスキップする

            if ($file->isTestFile()) {
                /* @var TestClassFile $file  */
                $className = $resolver->findTestAbsoluteClassName($file);
            } else {
                /* @var ClassFile $file  */
                $testFile = $finder->findTestFileByProdFilePath($file);
                $className = $resolver->findTestAbsoluteClassName($testFile);
            }

//            echo "executed: " . $className . PHP_EOL;

            $classList[$className] = 1;
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
