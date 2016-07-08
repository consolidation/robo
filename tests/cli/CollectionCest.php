<?php
namespace Robo;

use \CliGuy;

use Robo\Contract\TaskInterface;
use Robo\Collection\Temporary;
use Robo\Result;

class CollectionCest
{
    public function _before(CliGuy $I)
    {
        $I->getContainer()->addServiceProvider(\Robo\Collection\Collection::getCollectionServices());
        $I->getContainer()->addServiceProvider(\Robo\Task\File\loadTasks::getFileServices());
        $I->getContainer()->addServiceProvider(\Robo\Task\Filesystem\loadTasks::getFilesystemServices());

        $I->amInPath(codecept_data_dir().'sandbox');
    }

    public function toRunMultipleTasksViaATaskBuilder(CliGuy $I)
    {
        // This tests creating multiple tasks in a single builder,
        // which implicitly adds them to a collection.  To keep things
        // simple, we are only going to use taskFilesystemStack.  It
        // would be possible, of course, to do these operations with
        // a single FilesystemStack, but our goal is to test creating
        // multiple tasks with a builder, and ensure that a propper
        // collection is built.
        $builder = $I->builder();
        $result = $builder->taskFilesystemStack()
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

    public function toUseAWorkingDirWithATaskBuilder(CliGuy $I)
    {
        // Run the same test with a working directory.  The working
        // directory path will point to a temporary directory which
        // will be moved into place once the tasks complete.
        $builder = $I->builder();
        $workDirPath = $builder->workDir("build");
        $I->assertNotEquals("build", basename($workDirPath));
        $result = $builder->taskFilesystemStack()
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

    public function toRollbackAfterFailureViaATaskBuilder(CliGuy $I)
    {
        // This is like the previous test, toRunMultipleTasksViaATaskBuilder,
        // except we force an error at the end, and confirm that the
        // rollback function is called.
        $builder = $I->builder();
        $result = $builder->taskFilesystemStack()
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

    public function toRollbackANestedCollection(CliGuy $I)
    {
        // This is like the previous test, toRunMultipleTasksViaATaskBuilder,
        // except we force an error at the end, and confirm that the
        // rollback function is called.
        $builder = $I->builder();
        $builder->taskFilesystemStack()
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

        $result = $I->builder()
            ->taskFilesystemStack()
                ->mkdir('q')
                ->touch('q/q.txt')
            ->addTask($builder)
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
        $collection = $I->getContainer()->get('collection');

        // Set up a filesystem stack, but use addToCollection() to defer execution
        $I->taskFilesystemStack()
            ->mkdir('log')
            ->touch('log/error.txt')
            ->addToCollection($collection);

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
        $collection = $I->getContainer()->get('collection');

        // Get a temporary directory to work in. Note that we get a
        // name back, but the directory is not created until the task
        // runs.  This technically is not thread-safe, but we create
        // a random name, so it is unlikely to conflict.
        $tmpPath = $I->taskTmpDir()
            ->addToCollection($collection)
            ->getPath();

        // We can create the temporary directory early by running
        // 'runWithoutCompletion()'.  n.b. if we called 'run()' at
        // this point, the collection's 'complete()' method would be
        // called, and the temporary directory would be deleted.
        $mktmpResult = $collection->runWithoutCompletion();
        $I->assertEquals($mktmpResult['path'], $tmpPath, "Tmp dir result matches accessor.");
        $I->seeDirFound($tmpPath);

        // Set up a filesystem stack, but use addToCollection() to defer execution
        $I->taskFilesystemStack()
            ->mkdir("$tmpPath/tmp")
            ->touch("$tmpPath/tmp/error.txt")
            ->rename("$tmpPath/tmp", "$tmpPath/log")
            ->addToCollection($collection);

        // Copy our tmp directory to a location that is not transient
        $I->taskCopyDir([$tmpPath => 'copied'])
            ->addToCollection($collection);

        // FilesystemStack has not run yet, so no files should be found.
        $I->dontSeeFileFound("$tmpPath/tmp/error.txt");
        $I->dontSeeFileFound("$tmpPath/log/error.txt");
        $I->dontSeeFileFound('copied/log/error.txt');

        // Run the task collection
        $result = $collection->run();
        $I->assertEquals(0, $result->getExitCode(), $result->getMessage());

        // The file 'error.txt' should have been copied into the "copied" dir
        $I->seeFileFound('copied/log/error.txt');
        // $tmpPath should be deleted after $collection->run() completes.
        $I->dontSeeFileFound("$tmpPath/tmp/error.txt");
        $I->dontSeeFileFound("$tmpPath/log/error.txt");
    }

    public function toUseATmpDirAndChangeWorkingDirectory(CliGuy $I)
    {
        // Set up a collection to add tasks to
        $collection = $I->getContainer()->get('collection');

        $cwd = getcwd();

        // Get a temporary directory to work in. Note that we get a
        // name back, but the directory is not created until the task
        // runs.  This technically is not thread-safe, but we create
        // a random name, so it is unlikely to conflict.
        $tmpPath = $I->taskTmpDir()
            ->cwd()
            ->addToCollection($collection)
            ->getPath();

        // Set up a filesystem stack, but use addToCollection() to defer execution.
        // Note that since we used 'cwd()' above, the relative file paths
        // used below will be inside the temporary directory.
        $I->taskFilesystemStack()
            ->mkdir("log")
            ->touch("log/error.txt")
            ->addToCollection($collection);

        // Copy our tmp directory to a location that is not transient
        $I->taskCopyDir(['log' => "$cwd/copied2"])
            ->addToCollection($collection);

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
        $collection = $I->getContainer()->get('collection');

        // Write to a temporary file. Note that we can get the path
        // to the tempoary file that will be created, even though the
        // the file is not created until the task collecction runs.
        $tmpPath = $I->taskTmpFile('tmp', '.txt')
            ->line("This is a test file")
            ->addToCollection($collection)
            ->getPath();

        // Copy our tmp directory to a location that is not transient
        $I->taskFilesystemStack()
            ->copy($tmpPath, 'copied.txt')
            ->addToCollection($collection);

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
        $collection = $I->getContainer()->get('collection');

        // This test is equivalent to toUseATmpDirAndConfirmItIsDeleted,
        // but uses a different technique to create a collection of tasks.
        // We start off the same way, using addToCollection() to add our temporary
        // directory task to the collection, so that we have easy access to the
        // temporary directory's path via the getPath() method.
        $tmpPath = $I->taskTmpDir()
            ->addToCollection($collection)
            ->getPath();

        // Now, rather than creating a series of tasks and adding them
        // all with addToCollection(), we will add them directly to the collection
        // via the add() method.
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
}
