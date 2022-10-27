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


    private array $dependentFiles;
    private $finder;
    private $resolver;

    /**
     * @param ClassFile $file
     * @return (ClassFile|TestClassFile)[]
     */
    public function findAllDependedFiles(ClassFile $file): array
    {
        $this->finder = new Finder();
        $this->resolver = new ClassNameResolver();

        var_dump('');
        $absoluteClassName = $this->resolver->resolveAbsoluteClassName($file);
        $files = $this->finder->findDependedFiles($absoluteClassName);

        $this->dependentFiles[$file->getFilename()] = 1;
        var_dump('------- rec start');

        $unique = array_unique($this->recursive($files));
        sort($unique);
        return $unique;

//        $filenameList = $this->findDependedFilesRecursive($file, []);
//        $unique = array_unique($filenameList);
//        sort($unique);
//
//        return array_map(fn ($filename) => (new FileFactory())->create($filename), $unique);
    }

    public function recursive($files): array
    {
        foreach ($files as $file) {
            $filename = $file->getFilename();
            var_dump('targ | ' . $filename);
            if (isset($this->dependentFiles[$filename])) {
                var_dump('skip | ' . $filename);
                continue;
            }
            $this->dependentFiles[$filename] = 1;

            // TestFile は終端なので何にも依存されていない
            // git grep の回数を減らして、実行速度を上げる
            if ($file->isTestFile()) {
                var_dump('skip | ' . $filename);
                continue;
            }
            var_dump('chec | ' . $filename);
            $absoluteClassName = $this->resolver->resolveAbsoluteClassName($file);
            $newFileList = $this->finder->findDependedFiles($absoluteClassName);
            if (count($newFileList) === 0) {
                continue;
            }
            var_dump('------- child rec start');
            $this->recursive($newFileList);
            var_dump('------- child rec end');
        }

        return array_keys($this->dependentFiles);
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
        $git = new GitManager();
        $factory = new FileFactory();

        $classList = [];
        $filenameList = [];
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
            $filenameList[$filename] = 1;

            if ($file->isTestFile()) {
                /* @var TestClassFile $file */
                $absoluteTestClassName = $resolver->resolveAbsoluteClassName($file);
            } else {
                /* @var ClassFile $file */
                $testFile = $finder->findTestFileByClassFile($file);
                $absoluteTestClassName = $resolver->resolveAbsoluteClassName($testFile);
            }

//            foreach ($dependedFiles as $dependedFile) {
//                $filenameList[$dependedFile->getFilename()] = 1;
//
//            }

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
