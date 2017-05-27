<?php
namespace Robo;

use \CliGuy;

use Robo\Collection\Temporary;

class CollectionCest
{
    public function _before(CliGuy $I)
    {
        $I->amInPath(codecept_data_dir().'sandbox');
    }

    public function toRunMultipleTasksViaACollectionBuilder(CliGuy $I)
    {
        // This tests creating multiple tasks in a single builder,
        // which implicitly adds them to a collection.  To keep things
        // simple, we are only going to use taskFilesystemStack.  It
        // would be possible, of course, to do these operations with
        // a single FilesystemStack, but our goal is to test creating
        // multiple tasks with a builder, and ensure that a propper
        // collection is built.
        $collection = $I->collectionBuilder();
        $result = $collection->taskFilesystemStack()
                ->mkdir('a')
                ->touch('a/a.txt')
            ->rollback(
                $I->taskDeleteDir('a')
            )
            ->taskFilesystemStack()
                ->mkdir('a/b')
                ->touch('a/b/b.txt')
            ->taskFilesystemStack()
                ->mkdir('a/c')
                ->touch('a/c/c.txt')
            ->run();

        $I->assertEquals(0, $result->getExitCode(), $result->getMessage());

        // All of the tasks created by the builder should be added
        // to a collection, and `run()` should run them all.
        $I->seeDirFound('a');
        $I->seeFileFound('a/a.txt');
        $I->seeDirFound('a/b');
        $I->seeFileFound('a/b/b.txt');
        $I->seeDirFound('a/c');
        $I->seeFileFound('a/c/c.txt');
    }

    public function toUseAWorkingDirWithACollectionBuilder(CliGuy $I)
    {
        // Run the same test with a working directory.  The working
        // directory path will point to a temporary directory which
        // will be moved into place once the tasks complete.
        $collection = $I->collectionBuilder();
        $workDirPath = $collection->workDir("build");
        $I->assertNotEquals("build", basename($workDirPath));
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

        $I->assertEquals(0, $result->getExitCode(), $result->getMessage());

        // All of the tasks created by the builder should be added
        // to a collection, and `run()` should run them all.
        $I->seeDirFound('build/a');
        $I->seeFileFound('build/a/a.txt');
        $I->seeDirFound('build/a/b');
        $I->seeFileFound('build/a/b/b.txt');
        $I->seeDirFound('build/a/c');
        $I->seeFileFound('build/a/c/c.txt');
    }

    public function toRollbackAfterFailureViaACollectionBuilder(CliGuy $I)
    {
        // This is like the previous test, toRunMultipleTasksViaACollectionBuilder,
        // except we force an error at the end, and confirm that the
        // rollback function is called.
        $collection = $I->collectionBuilder();
        $result = $collection->taskFilesystemStack()
                ->mkdir('j')
                ->touch('j/j.txt')
            ->rollback(
                $I->taskDeleteDir('j')
            )
            ->taskFilesystemStack()
                ->mkdir('j/k')
                ->touch('j/k/k.txt')
            ->taskFilesystemStack()
                ->mkdir('j/k/m')
                ->touch('j/k/m/m.txt')
            ->taskCopyDir(['doesNotExist' => 'copied'])
            ->run();

        $I->assertEquals(1, $result->getExitCode(), $result->getMessage());

        // All of the tasks created by the builder should be added
        // to a collection, and `run()` should run them all.
        $I->dontSeeFileFound('q/q.txt');
        $I->dontSeeFileFound('j/j.txt');
        $I->dontSeeFileFound('j/k/k.txt');
        $I->dontSeeFileFound('j/k/m/m.txt');
    }

    public function toRollbackAWorkingDir(CliGuy $I)
    {
        // Run the same test with a working directory.  The working
        // directory path will point to a temporary directory which
        // will be moved into place once the tasks complete.
        $collection = $I->collectionBuilder();
        $workDirPath = $collection->workDir("build");
        $I->assertNotEquals("build", basename($workDirPath));
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

        $I->assertEquals(1, $result->getExitCode(), $result->getMessage());

        // All of the tasks created by the builder should be added
        // to a collection, and `run()` should run them all.
        $I->dontSeeFileFound('build/a');
        $I->dontSeeFileFound($workDirPath);
    }

    public function toBuildFilesViaAddIterable(CliGuy $I)
    {
        $processList = ['cats', 'dogs', 'sheep', 'fish', 'horses', 'cows'];

        $collection = $I->collectionBuilder();
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

        $I->assertEquals(0, $result->getExitCode(), $result->getMessage());

        $I->seeFileFound('stuff/cats.txt');
        $I->seeFileFound('stuff/dogs.txt');
        $I->seeFileFound('stuff/sheep.txt');
        $I->seeFileFound('stuff/fish.txt');
        $I->seeFileFound('stuff/horses.txt');
        $I->seeFileFound('stuff/cows.txt');
    }

    public function toRollbackANestedCollection(CliGuy $I)
    {
        // This is like the previous test, toRunMultipleTasksViaACollectionBuilder,
        // except we force an error at the end, and confirm that the
        // rollback function is called.
        $collection = $I->collectionBuilder();
        $collection->taskFilesystemStack()
                ->mkdir('j')
                ->touch('j/j.txt')
            ->rollback(
                $I->taskDeleteDir('j')
            )
            ->taskFilesystemStack()
                ->mkdir('j/k')
                ->touch('j/k/k.txt')
            ->taskFilesystemStack()
                ->mkdir('j/k/m')
                ->touch('j/k/m/m.txt');

        $result = $I->collectionBuilder()
            ->taskFilesystemStack()
                ->mkdir('q')
                ->touch('q/q.txt')
            ->addTask($collection)
            ->taskCopyDir(['doesNotExist' => 'copied'])
            ->run();

        $I->assertEquals(1, $result->getExitCode(), $result->getMessage());

        // All of the tasks created by the builder should be added
        // to a collection, and `run()` should run them all.
        $I->seeFileFound('q/q.txt');
        $I->dontSeeFileFound('j/j.txt');
        $I->dontSeeFileFound('j/k/k.txt');
        $I->dontSeeFileFound('j/k/m/m.txt');
    }

    public function toCreateDirViaCollection(CliGuy $I)
    {
        // Set up a collection to add tasks to
        $collection = $I->collectionBuilder();

        // Set up a filesystem stack
        $collection->taskFilesystemStack()
            ->mkdir('log')
            ->touch('log/error.txt');

        // FilesystemStack has not run yet, so file should not be found.
        $I->dontSeeFileFound('log/error.txt');

        // Run the task collection; now the files should be present
        $collection->run();
        $I->seeFileFound('log/error.txt');
        $I->seeDirFound('log');
    }

    public function toUseATmpDirAndConfirmItIsDeleted(CliGuy $I)
    {
        // Set up a collection to add tasks to
        $collection = $I->collectionBuilder();

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
        $I->dontSeeFileFound("$tmpPath/tmp/error.txt");
        $I->dontSeeFileFound("$tmpPath/log/error.txt");
        $I->dontSeeFileFound('copied/log/error.txt');

        // Run the task collection
        $result = $collection->run();
        $I->assertEquals(0, $result->getExitCode(), $result->getMessage());
        $I->assertEquals($result['path'], $tmpPath, "Tmp dir result matches accessor.");

        // The file 'error.txt' should have been copied into the "copied" dir.
        // This also proves that the tmp directory was created.
        $I->seeFileFound('copied/log/error.txt');
        // $tmpPath should be deleted after $collection->run() completes.
        $I->dontSeeFileFound("$tmpPath/tmp/error.txt");
        $I->dontSeeFileFound("$tmpPath/log/error.txt");
        $I->dontSeeFileFound("$tmpPath");
    }

    public function toUseATmpDirAndChangeWorkingDirectory(CliGuy $I)
    {
        // Set up a collection to add tasks to
        $collection = $I->collectionBuilder();

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
        $I->dontSeeFileFound("$tmpPath/log/error.txt");
        $I->dontSeeFileFound('$cwd/copied2/log/error.txt');

        // Run the task collection
        $result = $collection->run();
        $I->assertEquals(0, $result->getExitCode(), $result->getMessage());

        // The file 'error.txt' should have been copied into the "copied" dir
        $I->seeFileFound("$cwd/copied2/error.txt");
        // $tmpPath should be deleted after $collection->run() completes.
        $I->dontSeeFileFound("$tmpPath/log/error.txt");
        // Make sure that 'log' was created in the temporary directory, not
        // at the current working directory.
        $I->dontSeeFileFound("$cwd/log/error.txt");

        // Make sure that our working directory was restored.
        $finalWorkingDir = getcwd();
        $I->assertEquals($cwd, $finalWorkingDir);
    }

    public function toCreateATmpFileAndConfirmItIsDeleted(CliGuy $I)
    {
        // Set up a collection to add tasks to
        $collection = $I->collectionBuilder();

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
        $I->dontSeeFileFound("$tmpPath");
        $I->dontSeeFileFound('copied.txt');

        // Run the task collection
        $result = $collection->run();
        $I->assertEquals(0, $result->getExitCode(), $result->getMessage());

        // The file 'copied.txt' should have been copied from the tmp file
        $I->seeFileFound('copied.txt');
        // $tmpPath should be deleted after $collection->run() completes.
        $I->dontSeeFileFound("$tmpPath");
    }

    public function toUseATmpDirWithAlternateSyntax(CliGuy $I)
    {
        $collection = $I->collectionBuilder();

        // This test is equivalent to toUseATmpDirAndConfirmItIsDeleted,
        // but uses a different technique to create a collection of tasks.
        $tmpPath = $collection->tmpDir();

        // Now, rather than creating the tasks with a collection builder,
        // which automatically adds the tasks to the collection as they are
        // created, we will instead create them individually and then add
        // them to the collection via the addTaskList() method.
        $result = $collection->addTaskList(
            [
                $I->taskFilesystemStack()->mkdir("$tmpPath/log")->touch("$tmpPath/log/error.txt"),
                $I->taskCopyDir([$tmpPath => 'copied3']),
            ]
        )->run();

        // The results of this operation should be the same.
        $I->assertEquals(0, $result->getExitCode(), $result->getMessage());
        $I->seeFileFound('copied3/log/error.txt');
        $I->dontSeeFileFound("$tmpPath/log/error.txt");
    }

    public function toCreateATmpDirWithoutACollection(CliGuy $I)
    {
        // Create a temporary directory, using our function name as
        // the prefix for the directory name.
        $tmpDirTask = $I->taskTmpDir(__FUNCTION__);
        $tmpPath = $tmpDirTask->getPath();
        $I->dontSeeFileFound($tmpPath);
        $tmpDirTask->run();
        $I->seeDirFound($tmpPath);
        // Creating a temporary directory without a task collection will
        // cause the temporary directory to be deleted when the program
        // terminates.  We can force it to clean up sooner by calling
        // TransientManager::complete(); note that this deletes ALL global tmp
        // directories, so this is not thread-safe!  Useful in tests, though.
        Temporary::complete();
        $I->dontSeeFileFound($tmpPath);
    }

    public function toCreateATmpDirUsingShortcut(CliGuy $I)
    {
        // Create a temporary directory, using our function name as
        // the prefix for the directory name.
        $tmpPath = $I->shortcutTmpDir(__FUNCTION__);
        $I->seeDirFound($tmpPath);
        // Creating a temporary directory without a task collection will
        // cause the temporary directory to be deleted when the program
        // terminates.  We can force it to clean up sooner by calling
        // TransientManager::complete(); note that this deletes ALL global tmp
        // directories, so this is not thread-safe!  Useful in tests, though.
        Temporary::complete();
        $I->dontSeeFileFound($tmpPath);
    }

    public function toThrowAnExceptionAndConfirmItIsCaught(CliGuy $I)
    {
        $collection = $I->getContainer()->get('collection');

        $collection->addCode(
            function () {
                throw new \RuntimeException('Error');
            }
        );
        $result = $collection->run();
        $I->assertEquals('Error', $result->getMessage());
        $I->assertEquals(1, $result->getExitCode());
    }

    public function toChainData(CliGuy $I)
    {
        $collection = $I->collectionBuilder();

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

        $state = $collection->getState();
        $I->assertEquals('1st', $state['one']);
        $I->assertEquals('1st', $state['item']);
        $I->assertEquals('2nd', $state['final']);
    }
}
