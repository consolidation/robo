<?php
use \CliGuy;

use Robo\Contract\TaskInterface;
use Robo\Result;

class TaskCollectionCest
{
    public function _before(CliGuy $I)
    {
        $I->amInPath(codecept_data_dir().'sandbox');
    }

    public function toCreateDirViaCollection(CliGuy $I)
    {
        // Set up a collection to add tasks to
        $collection = $I->taskCollection();

        // Set up a filesystem stack, but use runLater() to defer execution
        $I->taskFileSystemStack()
            ->mkdir('log')
            ->touch('log/error.txt')
            ->runLater($collection);

        // FileSystemStack has not run yet, so file should not be found.
        $I->dontSeeFileFound('log/error.txt');

        // Run the task collection; now the files should be present
        $collection->runNow();
        $I->seeFileFound('log/error.txt');
        $I->seeDirFound('log');
    }

    public function toUseATmpDirAndConfirmItIsDeleted(CliGuy $I)
    {
        // Set up a collection to add tasks to
        $collection = $I->taskCollection();

        // Get a temporary directory to work in. Note that we get a
        // name back, but the directory is not created until the task
        // runs.  This technically is not thread-safe, but we create
        // a random name, so it is unlikely to conflict.
        $tmpPath = $I->taskTmpDir()
            ->runLater($collection)
            ->getPath();

        // Set up a filesystem stack, but use runLater() to defer execution
        $I->taskFileSystemStack()
            ->mkdir("$tmpPath/log")
            ->touch("$tmpPath/log/error.txt")
            ->runLater($collection);

        // Copy our tmp directory to a location that is not transient
        $I->taskCopyDir([$tmpPath => 'copied'])
            ->runLater($collection);

        // FileSystemStack has not run yet, so no files should be found.
        $I->dontSeeFileFound("$tmpPath/log/error.txt");
        $I->dontSeeFileFound('copied/log/error.txt');

        // Run the task collection
        $result = $collection->runNow();
        $I->assertEquals(0, $result->getExitCode(), $result->getMessage());

        // The file 'error.txt' should have been copied into the "copied" dir
        $I->seeFileFound('copied/log/error.txt');
        // $tmpPath should be deleted after $collection->runNow() completes.
        $I->dontSeeFileFound("$tmpPath/log/error.txt");
    }

    public function toUseATmpDirAndChangeWorkingDirectory(CliGuy $I)
    {
        // Set up a collection to add tasks to
        $collection = $I->taskCollection();

        $cwd = getcwd();

        // Get a temporary directory to work in. Note that we get a
        // name back, but the directory is not created until the task
        // runs.  This technically is not thread-safe, but we create
        // a random name, so it is unlikely to conflict.
        $tmpPath = $I->taskTmpDir()
            ->cwd()
            ->runLater($collection)
            ->getPath();

        // Set up a filesystem stack, but use runLater() to defer execution.
        // Note that since we used 'cwd()' above, the relative file paths
        // used below will be inside the temporary directory.
        $I->taskFileSystemStack()
            ->mkdir("log")
            ->touch("log/error.txt")
            ->runLater($collection);

        // Copy our tmp directory to a location that is not transient
        $I->taskCopyDir(['log' => "$cwd/copied2"])
            ->runLater($collection);

        // FileSystemStack has not run yet, so no files should be found.
        $I->dontSeeFileFound("$tmpPath/log/error.txt");
        $I->dontSeeFileFound('$cwd/copied2/log/error.txt');

        // Run the task collection
        $result = $collection->runNow();
        $I->assertEquals(0, $result->getExitCode(), $result->getMessage());

        // The file 'error.txt' should have been copied into the "copied" dir
        $I->seeFileFound("$cwd/copied2/error.txt");
        // $tmpPath should be deleted after $collection->runNow() completes.
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
        $collection = $I->taskCollection();

        // Write to a temporary file. Note that we can get the path
        // to the tempoary file that will be created, even though the
        // the file is not created until the task collecction runs.
        $tmpPath = $I->taskTmpFile('tmp', '.txt')
            ->line("This is a test file")
            ->runLater($collection)
            ->getPath();

        // Copy our tmp directory to a location that is not transient
        $I->taskFileSystemStack()
            ->copy($tmpPath, 'copied.txt')
            ->runLater($collection);

        // FileSystemStack has not run yet, so no files should be found.
        $I->dontSeeFileFound("$tmpPath");
        $I->dontSeeFileFound('copied.txt');

        // Run the task collection
        $result = $collection->runNow();
        $I->assertEquals(0, $result->getExitCode(), $result->getMessage());

        // The file 'copied.txt' should have been copied from the tmp file
        $I->seeFileFound('copied.txt');
        // $tmpPath should be deleted after $collection->run() completes.
        $I->dontSeeFileFound("$tmpPath");
    }

    public function toUseATmpDirWithAlternateSyntax(CliGuy $I)
    {
        $collection = $I->taskCollection();

        // This test is equivalent to toUseATmpDirAndConfirmItIsDeleted,
        // but uses a different technique to create a collection of tasks.
        // We start off the same way, using runLater() to add our temporary
        // directory task to the collection, so that we have easy access to the
        // temporary directory's path via the getPath() method.
        $tmpPath = $I->taskTmpDir()
            ->runLater($collection)
            ->getPath();

        // Now, rather than creating a series of tasks and adding them
        // all with runLater(), we will add them directly to the collection
        // via the add() method.
        $result = $collection->add(
            [
                $I->taskFileSystemStack()->mkdir("$tmpPath/log")->touch("$tmpPath/log/error.txt"),
                $I->taskCopyDir([$tmpPath => 'copied3']),
            ]
        )->runNow();

        // The results of this operation should be the same.
        $I->assertEquals(0, $result->getExitCode(), $result->getMessage());
        $I->seeFileFound('copied3/log/error.txt');
        $I->dontSeeFileFound("$tmpPath/log/error.txt");
    }

}
