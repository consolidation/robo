<?php
namespace Robo;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use Robo\Collection\Temporary;
use Robo\Exception\AbortTasksException;
use Robo\Traits\TestTasksTrait;

class CollectionTest extends TestCase
{
    use TestTasksTrait;
    use Collection\Tasks;
    use Task\File\Tasks;
    use Task\Filesystem\Tasks;
    use Task\Filesystem\Shortcuts;
    use Task\TestHelperTasks;

    protected $fixtures;

    public function setUp(): void
    {
        $this->fixtures = new Fixtures();
        $this->initTestTasksTrait();
        $this->fixtures->createAndCdToSandbox();
    }

    public function tearDown(): void
    {
        $this->fixtures->cleanup();
    }

    public function testSimulateDirCreation()
    {
        // Set up a collection to add tasks to
        $collection = $this->collectionBuilderForTest();
        $collection->simulated(true);

        // Set up a filesystem stack
        $collection->taskFilesystemStack()
            ->mkdir('simulatedir')
            ->touch('simulatedir/error.txt');

        // Run the task collection; the files would be present were this
        // operation not simulated.
        $result = $collection->run();
        $this->assertTrue($result->wasSuccessful());
        // Nothing should be created in simulated mode
        $this->assertFileDoesNotExist('simulatedir/error.txt');
        $this->assertOutputContains('[Simulator] Simulating Filesystem\FilesystemStack()');
    }


    public function testRunMultipleTasksViaACollectionBuilder()
    {
        // This tests creating multiple tasks in a single builder,
        // which implicitly adds them to a collection.  To keep things
        // simple, we are only going to use taskFilesystemStack.  It
        // would be possible, of course, to do these operations with
        // a single FilesystemStack, but our goal is to test creating
        // multiple tasks with a builder, and ensure that a propper
        // collection is built.
        $collection = $this->collectionBuilderForTest();
        $result = $collection->taskFilesystemStack()
                ->mkdir('a')
                ->touch('a/a.txt')
            ->rollback(
                $this->taskDeleteDir('a')
            )
            ->taskFilesystemStack()
                ->mkdir('a/b')
                ->touch('a/b/b.txt')
            ->taskFilesystemStack()
                ->mkdir('a/c')
                ->touch('a/c/c.txt')
            ->run();

        $this->assertTrue($result->wasSuccessful(), $result->getMessage());

        $this->assertEquals(0, $result->getExitCode());

        // All of the tasks created by the builder should be added
        // to a collection, and `run()` should run them all.
        $this->assertFileExists('a');
        $this->assertFileExists('a/a.txt');
        $this->assertFileExists('a/b');
        $this->assertFileExists('a/b/b.txt');
        $this->assertFileExists('a/c');
        $this->assertFileExists('a/c/c.txt');
    }

    public function testUsingAWorkingDirWithACollectionBuilder()
    {
        // Run the same test with a working directory.  The working
        // directory path will point to a temporary directory which
        // will be moved into place once the tasks complete.
        $collection = $this->collectionBuilderForTest();
        $workDirPath = $collection->workDir("build");
        $this->assertNotEquals("build", basename($workDirPath));
        $result = $collection->taskFilesystemStack()
                ->mkdir("{$workDirPath}/a")
                ->touch("{$workDirPath}/a/a.txt")
            ->taskFilesystemStack()
                ->mkdir("{$workDirPath}/a/b")
                ->touch("{$workDirPath}/a/b/b.txt")
            ->taskFilesystemStack()
                ->mkdir("{$workDirPath}/a/c")
                ->touch("{$workDirPath}/a/c/c.txt")
            ->run();

        $this->assertTrue($result->wasSuccessful(), $result->getMessage());

        // All of the tasks created by the builder should be added
        // to a collection, and `run()` should run them all.
        $this->assertFileExists('build/a');
        $this->assertFileExists('build/a/a.txt');
        $this->assertFileExists('build/a/b');
        $this->assertFileExists('build/a/b/b.txt');
        $this->assertFileExists('build/a/c');
        $this->assertFileExists('build/a/c/c.txt');
    }

    public function testRollbackAfterFailureViaACollectionBuilder()
    {
        // This is like the previous test, toRunMultipleTasksViaACollectionBuilder,
        // except we force an error at the end, and confirm that the
        // rollback function is called.
        $collection = $this->collectionBuilderForTest();
        $result = $collection->taskFilesystemStack()
                ->mkdir('j')
                ->touch('j/j.txt')
            ->rollback(
                $this->taskDeleteDir('j')
            )
            ->taskFilesystemStack()
                ->mkdir('j/k')
                ->touch('j/k/k.txt')
            ->taskFilesystemStack()
                ->mkdir('j/k/m')
                ->touch('j/k/m/m.txt')
            ->taskCopyDir(['doesNotExist' => 'copied'])
            ->run();

        $this->assertEquals(1, $result->getExitCode(), $result->getMessage());

        // All of the tasks created by the builder should be added
        // to a collection, and `run()` should run them all.
        $this->assertFileDoesNotExist('q/q.txt');
        $this->assertFileDoesNotExist('j/j.txt');
        $this->assertFileDoesNotExist('j/k/k.txt');
        $this->assertFileDoesNotExist('j/k/m/m.txt');
    }

    public function testAbortRollbackOrCompletion()
    {
        // This is like the previous test, except we throw a ForcedException()
        // inside the rollback to abort the rollback.
        $collection = $this->collectionBuilderForTest();
        $result = $collection->taskFilesystemStack()
            ->mkdir('j')
            ->touch('j/j.txt')
            ->rollback(
                $this->taskDeleteDir('j')
            )
            ->rollbackCode(function () {
                throw new AbortTasksException('Aborting rollback.');
            })
            ->taskFilesystemStack()
            ->mkdir('j/k')
            ->touch('j/k/k.txt')
            ->taskFilesystemStack()
            ->mkdir('j/k/m')
            ->touch('j/k/m/m.txt')
            ->taskCopyDir(['doesNotExist' => 'copied'])
            ->run();

        $this->assertEquals(1, $result->getExitCode(), $result->getMessage());

        // All of the tasks created by the builder should be added
        // to a collection, and `run()` should run them all.
        $this->assertFileExists('j/j.txt');
        $this->assertFileExists('j/k/k.txt');
        $this->assertFileExists('j/k/m/m.txt');
    }

    public function testRollbackAWorkingDir()
    {
        // Run the same test with a working directory.  The working
        // directory path will point to a temporary directory which
        // will be moved into place once the tasks complete.
        $collection = $this->collectionBuilderForTest();
        $workDirPath = $collection->workDir("build");
        $this->assertNotEquals("build", basename($workDirPath));
        $result = $collection->taskFilesystemStack()
                ->mkdir("{$workDirPath}/a")
                ->touch("{$workDirPath}/a/a.txt")
            ->taskFilesystemStack()
                ->mkdir("{$workDirPath}/a/b")
                ->touch("{$workDirPath}/a/b/b.txt")
            ->taskFilesystemStack()
                ->mkdir("{$workDirPath}/a/c")
                ->touch("{$workDirPath}/a/c/c.txt")
            ->taskCopyDir(['doesNotExist' => 'copied'])
            ->run();

        $this->assertEquals(1, $result->getExitCode(), $result->getMessage());

        // All of the tasks created by the builder should be added
        // to a collection, and `run()` should run them all.
        $this->assertFileDoesNotExist('build/a');
        $this->assertFileDoesNotExist($workDirPath);
    }

    public function testBuildFilesViaAddIterable()
    {
        $processList = ['cats', 'dogs', 'sheep', 'fish', 'horses', 'cows'];

        $collection = $this->collectionBuilderForTest();
        $result = $collection
            ->taskFilesystemStack()
                ->mkdir('stuff')
            ->taskForEach($processList)
                ->withBuilder(
                    function ($builder, $key, $value) {
                        return $builder
                            ->taskFilesystemStack()
                                ->touch("stuff/{$value}.txt");
                    }
                )
            ->run();

        $this->assertEquals(0, $result->getExitCode(), $result->getMessage());

        $this->assertFileExists('stuff/cats.txt');
        $this->assertFileExists('stuff/dogs.txt');
        $this->assertFileExists('stuff/sheep.txt');
        $this->assertFileExists('stuff/fish.txt');
        $this->assertFileExists('stuff/horses.txt');
        $this->assertFileExists('stuff/cows.txt');
    }

    public function testRollbackANestedCollection()
    {
        // This is like the previous test, toRunMultipleTasksViaACollectionBuilder,
        // except we force an error at the end, and confirm that the
        // rollback function is called.
        $collection = $this->collectionBuilderForTest();
        $collection->taskFilesystemStack()
                ->mkdir('j')
                ->touch('j/j.txt')
            ->rollback(
                $this->taskDeleteDir('j')
            )
            ->taskFilesystemStack()
                ->mkdir('j/k')
                ->touch('j/k/k.txt')
            ->taskFilesystemStack()
                ->mkdir('j/k/m')
                ->touch('j/k/m/m.txt');

        $result = $this->collectionBuilderForTest()
            ->taskFilesystemStack()
                ->mkdir('q')
                ->touch('q/q.txt')
            ->addTask($collection)
            ->taskCopyDir(['doesNotExist' => 'copied'])
            ->run();

        $this->assertEquals(1, $result->getExitCode(), $result->getMessage());

        // All of the tasks created by the builder should be added
        // to a collection, and `run()` should run them all.
        $this->assertFileExists('q/q.txt');
        $this->assertFileDoesNotExist('j/j.txt');
        $this->assertFileDoesNotExist('j/k/k.txt');
        $this->assertFileDoesNotExist('j/k/m/m.txt');
    }

    public function testRollbackInCorrectOrder()
    {
        $expected_order = [6,5,4,3,2,1];
        $actual_order = [];
        $collection = $this->collectionBuilderForTest();
        $collection->rollbackCode(function () use (&$actual_order) {
            $actual_order[] = 1;
        });
        $collection->rollbackCode(function () use (&$actual_order) {
            $actual_order[] = 2;
        });
        $collection->rollbackCode(function () use (&$actual_order) {
            $actual_order[] = 3;
        });
        // Add a nested collection with rollbacks.
        $nested_collection = $this->collectionBuilderForTest();
        $nested_collection->rollbackCode(function () use (&$actual_order) {
            $actual_order[] = 4;
        });
        $nested_collection->rollbackCode(function () use (&$actual_order) {
            $actual_order[] = 5;
        });
        $collection->addTask($nested_collection);

        $collection->rollbackCode(function () use (&$actual_order) {
            $actual_order[] = 6;
        });
        $collection->addCode(function () {
            return Result::EXITCODE_ERROR;
        });
        $result = $collection->run();
        $this->assertFalse($result->wasSuccessful(), $result->getMessage());
        $this->assertEquals($expected_order, $actual_order);
    }

    public function testCreateDirViaCollection()
    {
        // Set up a collection to add tasks to
        $collection = $this->collectionBuilderForTest();

        // Set up a filesystem stack
        $collection->taskFilesystemStack()
            ->mkdir('log')
            ->touch('log/error.txt');

        // FilesystemStack has not run yet, so file should not be found.
        $this->assertFileDoesNotExist('log/error.txt');

        // Run the task collection; now the files should be present
        $result = $collection->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('log/error.txt');
        $this->assertFileExists('log');
    }

    public function testUseATmpDirAndConfirmItIsDeleted()
    {
        // Set up a collection to add tasks to
        $collection = $this->collectionBuilderForTest();

        // Get a temporary directory to work in. Note that we get a
        // name back, but the directory is not created until the task
        // runs.  This technically is not thread-safe, but we create
        // a random name, so it is unlikely to conflict.
        $tmpPath = $collection->tmpDir();

        // Set up a filesystem stack, but use a collection to defer execution
        $collection->taskFilesystemStack()
            ->mkdir("$tmpPath/tmp")
            ->touch("$tmpPath/tmp/error.txt")
            ->rename("$tmpPath/tmp", "$tmpPath/log");

        // Copy our tmp directory to a location that is not transient
        $collection->taskCopyDir([$tmpPath => 'copied']);

        // FilesystemStack has not run yet, so no files should be found.
        $this->assertFileDoesNotExist("$tmpPath/tmp/error.txt");
        $this->assertFileDoesNotExist("$tmpPath/log/error.txt");
        $this->assertFileDoesNotExist('copied/log/error.txt');

        // Run the task collection
        $result = $collection->run();
        $this->assertEquals(0, $result->getExitCode(), $result->getMessage());
        $this->assertEquals($result['path'], $tmpPath, "Tmp dir result matches accessor.");

        // The file 'error.txt' should have been copied into the "copied" dir.
        // This also proves that the tmp directory was created.
        $this->assertFileExists('copied/log/error.txt');
        // $tmpPath should be deleted after $collection->run() completes.
        $this->assertFileDoesNotExist("$tmpPath/tmp/error.txt");
        $this->assertFileDoesNotExist("$tmpPath/log/error.txt");
        $this->assertFileDoesNotExist("$tmpPath");
    }

    public function testUseATmpDirAndChangeWorkingDirectory()
    {
        // Set up a collection to add tasks to
        $collection = $this->collectionBuilderForTest();

        $cwd = getcwd();

        $tmpPath = $collection->taskTmpDir()
            ->cwd()
            ->getPath();

        // Set up a filesystem stack, but use a collection to defer execution.
        // Note that since we used 'cwd()' above, the relative file paths
        // used below will be inside the temporary directory.
        $collection->taskFilesystemStack()
            ->mkdir("log")
            ->touch("log/error.txt");

        // Copy our tmp directory to a location that is not transient
        $collection->taskCopyDir(['log' => "$cwd/copied2"]);

        // FilesystemStack has not run yet, so no files should be found.
        $this->assertFileDoesNotExist("$tmpPath/log/error.txt");
        $this->assertFileDoesNotExist('$cwd/copied2/log/error.txt');

        // Run the task collection
        $result = $collection->run();
        $this->assertEquals(0, $result->getExitCode(), $result->getMessage());

        // The file 'error.txt' should have been copied into the "copied" dir
        $this->assertFileExists("$cwd/copied2/error.txt");
        // $tmpPath should be deleted after $collection->run() completes.
        $this->assertFileDoesNotExist("$tmpPath/log/error.txt");
        // Make sure that 'log' was created in the temporary directory, not
        // at the current working directory.
        $this->assertFileDoesNotExist("$cwd/log/error.txt");

        // Make sure that our working directory was restored.
        $finalWorkingDir = getcwd();
        $this->assertEquals($cwd, $finalWorkingDir);
    }

    public function testCreateATmpFileAndConfirmItIsDeleted()
    {
        // Set up a collection to add tasks to
        $collection = $this->collectionBuilderForTest();

        // Write to a temporary file. Note that we can get the path
        // to the tempoary file that will be created, even though the
        // the file is not created until the task collecction runs.
        $tmpPath = $collection->taskTmpFile('tmp', '.txt')
            ->line("This is a test file")
            ->getPath();

        // Copy our tmp directory to a location that is not transient
        $collection->taskFilesystemStack()
            ->copy($tmpPath, 'copied.txt');

        // FilesystemStack has not run yet, so no files should be found.
        $this->assertFileDoesNotExist("$tmpPath");
        $this->assertFileDoesNotExist('copied.txt');

        // Run the task collection
        $result = $collection->run();
        $this->assertEquals(0, $result->getExitCode(), $result->getMessage());

        // The file 'copied.txt' should have been copied from the tmp file
        $this->assertFileExists('copied.txt');
        // $tmpPath should be deleted after $collection->run() completes.
        $this->assertFileDoesNotExist("$tmpPath");
    }

    public function testUseATmpDirWithAlternateSyntax()
    {
        $collection = $this->collectionBuilderForTest();

        // This test is equivalent to toUseATmpDirAndConfirmItIsDeleted,
        // but uses a different technique to create a collection of tasks.
        $tmpPath = $collection->tmpDir();

        // Now, rather than creating the tasks with a collection builder,
        // which automatically adds the tasks to the collection as they are
        // created, we will instead create them individually and then add
        // them to the collection via the addTaskList() method.
        $result = $collection->addTaskList(
            [
                $this->taskFilesystemStack()->mkdir("$tmpPath/log")->touch("$tmpPath/log/error.txt"),
                $this->taskCopyDir([$tmpPath => 'copied3']),
            ]
        )->run();

        // The results of this operation should be the same.
        $this->assertEquals(0, $result->getExitCode(), $result->getMessage());
        $this->assertFileExists('copied3/log/error.txt');
        $this->assertFileDoesNotExist("$tmpPath/log/error.txt");
    }

    public function testCreateATmpDirWithoutACollection()
    {
        // Create a temporary directory, using our function name as
        // the prefix for the directory name.
        $tmpDirTask = $this->taskTmpDir(__FUNCTION__);
        $tmpPath = $tmpDirTask->getPath();
        $this->assertFileDoesNotExist($tmpPath);
        $result = $tmpDirTask->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists($tmpPath);
        // Creating a temporary directory without a task collection will
        // cause the temporary directory to be deleted when the program
        // terminates.  We can force it to clean up sooner by calling
        // TransientManager::complete(); note that this deletes ALL global tmp
        // directories, so this is not thread-safe!  Useful in tests, though.
        Temporary::complete();
        $this->assertFileDoesNotExist($tmpPath);
    }

    public function testCreateATmpDirUsingShortcut()
    {
        // Create a temporary directory, using our function name as
        // the prefix for the directory name.
        $tmpPath = $this->_tmpDir(__FUNCTION__);
        $this->assertFileExists($tmpPath);
        // Creating a temporary directory without a task collection will
        // cause the temporary directory to be deleted when the program
        // terminates.  We can force it to clean up sooner by calling
        // TransientManager::complete(); note that this deletes ALL global tmp
        // directories, so this is not thread-safe!  Useful in tests, though.
        Temporary::complete();
        $this->assertFileDoesNotExist($tmpPath);
    }

    public function testThrowAnExceptionAndConfirmItIsCaught()
    {
        $collection = $this->collectionBuilderForTest();

        $collection->addCode(
            function () {
                throw new \RuntimeException('Error');
            }
        );
        $result = $collection->run();
        $this->assertEquals('Error', $result->getMessage());
        $this->assertEquals(1, $result->getExitCode());
    }

    public function testChainData()
    {
        $collection = $this->collectionBuilderForTest();

        $result = $collection
            ->taskValueProvider()
                ->provideMessage('1st') // Sets Result's message to '1st'
                ->storeState('one') // Copy Result's message to $state['one']
            ->taskValueProvider()
                ->provideMessage('2nd')
                ->storeState('two')
            ->taskValueProvider()
                ->deferTaskConfiguration('provideItem', 'one') // Same as ->proivdeItem($state['one']), but runs immediately before this task's run() method.
                ->deferTaskConfiguration('provideMessage', 'two')
                ->storeState('final')
            ->run();

        $this->assertTrue($result->wasSuccessful(), $result->getMessage());

        $state = $collection->getState();
        $this->assertEquals('1st', $state['one']);
        $this->assertEquals('1st', $state['item']);
        $this->assertEquals('2nd', $state['final']);
    }

}


