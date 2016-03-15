<?php

class FileSystemStackCest
{
    public function _before(CliGuy $I)
    {
        $I->getContainer()->addServiceProvider(\Robo\Task\FileSystem\ServiceProvider::class);
        $I->amInPath(codecept_data_dir().'sandbox');
    }

    public function toCreateDir(CliGuy $I)
    {
        $I->task('FileSystemStack')
            ->mkdir('log')
            ->touch('log/error.txt')
            ->run();
        $I->seeFileFound('log/error.txt');
    }

    public function toDeleteFile(CliGuy $I)
    {
        $I->task('FileSystemStack')
            ->stopOnFail()
            ->remove('a.txt')
            ->run();
        $I->dontSeeFileFound('a.txt');
    }
}
