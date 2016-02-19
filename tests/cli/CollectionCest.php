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
        $I->amInPath(codecept_data_dir().'sandbox');
    }

    public function toCreateDirViaCollection(CliGuy $I)
    {
        // Set up a collection to add tasks to
        $collection = $I->collection();

        // Set up a filesystem stack, but use addToCollection() to defer execution
        $I->taskFileSystemStack()
            ->mkdir('log')
            ->touch('log/error.txt')
            ->addToCollection($collection);

        // FileSystemStack has not run yet, so file should not be found.
        $I->dontSeeFileFound('log/error.txt');

        // Run the task collection; now the files should be present
        $collection->run();
        $I->seeFileFound('log/error.txt');
        $I->seeDirFound('log');
    }

    public function toUseATmpDirAndConfirmItIsDeleted(CliGuy $I)
    {
        // Set up a collection to add tasks to
        $collection = $I->collection();

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
        $I->taskFileSystemStack()
            ->mkdir("$tmpPath/tmp")
            ->touch("$tmpPath/tmp/error.txt")
            ->rename("$tmpPath/tmp", "$tmpPath/log")
            ->addToCollection($collection);

        // Copy our tmp directory to a location that is not transient
        $I->taskCopyDir([$tmpPath => 'copied'])
            ->addToCollection($collection);

        // FileSystemStack has not run yet, so no files should be found.
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
        $collection = $I->collection();

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
        $I->taskFileSystemStack()
            ->mkdir("log")
            ->touch("log/error.txt")
            ->addToCollection($collection);

        // Copy our tmp directory to a location that is not transient
        $I->taskCopyDir(['log' => "$cwd/copied2"])
            ->addToCollection($collection);

        // FileSystemStack has not run yet, so no files should be found.
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
        $collection = $I->collection();

        // Write to a temporary file. Note that we can get the path
        // to the tempoary file that will be created, even though the
        // the file is not created until the task collecction runs.
        $tmpPath = $I->taskTmpFile('tmp', '.txt')
            ->line("This is a test file")
            ->addToCollection($collection)
            ->getPath();

        // Copy our tmp directory to a location that is not transient
        $I->taskFileSystemStack()
            ->copy($tmpPath, 'copied.txt')
            ->addToCollection($collection);

        // FileSystemStack has not run yet, so no files should be found.
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
        $collection = $I->collection();

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
        $result = $collection->add(
            [
                $I->taskFileSystemStack()->mkdir("$tmpPath/log")->touch("$tmpPath/log/error.txt"),
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
        $I->seeFileFound($tmpPath);
        // Creating a temporary directory without a task collection will
        // cause the temporary directory to be deleted when the program
        // terminates.  We can force it to clean up sooner by calling
        // TransientManager::complete(); note that this deletes ALL global tmp
        // directories, so this is not thread-safe!  Useful in tests, though.
        Temporary::complete();
        $I->dontSeeFileFound($tmpPath);
    }
}
