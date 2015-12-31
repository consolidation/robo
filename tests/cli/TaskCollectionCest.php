<?php
use \CliGuy;

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

        // Set up a filesystem stack, but 'collect' it rather than 'run' it
        $I->taskFileSystemStack()
            ->mkdir('log')
            ->touch('log/error.txt')
            ->collect($collection);

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
        $collection = $I->taskCollection();

        // Get a temporary directory to work in. Note that we get a
        // name back, but the directory is not created until the task
        // runs.  This technically is not thread-safe, but we create
        // a random name, so it is unlikely to conflict.
        $tmpPath = $I->taskTmpDir()
            ->collect($collection)
            ->getPath();

        // Set up a filesystem stack, but 'collect' it rather than 'run' it
        $I->taskFileSystemStack()
            ->mkdir("$tmpPath/log")
            ->touch("$tmpPath/log/error.txt")
            ->collect($collection);

        // Copy our tmp directory to a location that is not transient
        $I->taskCopyDir([$tmpPath => 'copied'])
            ->collect($collection);

        // FileSystemStack has not run yet, so no files should be found.
        $I->dontSeeFileFound("$tmpPath/log/error.txt");
        $I->dontSeeFileFound('copied/log/error.txt');

        // Run the task collection
        $result = $collection->run();
        $I->assertEquals(0, $result->getExitCode(), $result->getMessage());

        // The file 'error.txt' should have been copied into the "copied" dir
        $I->seeFileFound('copied/log/error.txt');
        // $tmpPath should be deleted after $collection->run() completes.
        $I->dontSeeFileFound("$tmpPath/log/error.txt");
    }


    public function toCreateATmpFileAndConfirmItIsDeleted(CliGuy $I)
    {
        // Set up a collection to add tasks to
        $collection = $I->taskCollection();

        // Get a temporary directory to work in. Note that we get a
        // name back, but the directory is not created until the task
        // runs.  This technically is not thread-safe, but we create
        // a random name, so it is unlikely to conflict.
        $tmpPath = $I->taskTmpFile('tmp', '.txt')
            ->line("This is a test file")
            ->collect($collection)
            ->getPath();

        // Copy our tmp directory to a location that is not transient
        $I->taskFileSystemStack()
            ->copy($tmpPath, 'copied.txt')
            ->collect($collection);

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
}
