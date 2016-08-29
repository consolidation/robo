<?php

class FilesystemStackCest
{
    public function _before(CliGuy $I)
    {
        $I->amInPath(codecept_data_dir().'sandbox');
    }

    public function toCreateDir(CliGuy $I)
    {
        $I->taskFilesystemStack()
            ->mkdir('log')
            ->touch('log/error.txt')
            ->run();
        $I->seeFileFound('log/error.txt');
    }

    public function toDeleteFile(CliGuy $I)
    {
        $I->taskFilesystemStack()
            ->stopOnFail()
            ->remove('a.txt')
            ->run();
        $I->dontSeeFileFound('a.txt');
    }

    public function toTestCrossVolumeRename(CliGuy $I)
    {
        $fsStack = $I->taskFilesystemStack()
            ->mkdir('log')
            ->touch('log/error.txt');
        $fsStack->run();

        // We can't force _rename to run the cross-volume
        // code path, so we will directly call the protected
        // method crossVolumeRename to test to ensure it works.
        // We will get a reference to it via reflection, set
        // the reflected method object to public, and then
        // call it via reflection.
        $class = new ReflectionClass('\Robo\Task\Filesystem\FilesystemStack');
        $method = $class->getMethod('crossVolumeRename');
        $method->setAccessible(true);
        $actualFsStackTask = $fsStack->getCollectionBuilderCurrentTask();
        $method->invokeArgs($actualFsStackTask, ['log', 'logfiles']);

        $I->dontSeeFileFound('log/error.txt');
        $I->seeFileFound('logfiles/error.txt');
    }
}
