<?php
use \CliGuy;

class FileSystemStackCest
{
    public function _before(CliGuy $I)
    {
        $I->amInPath(codecept_data_dir().'sandbox');
    }

    public function toCreateDir(CliGuy $I)
    {
        $I->taskFileSystemStack()
            ->mkdir('log')
            ->touch('log/error.txt')
            ->run();
        $I->seeFileFound('log/error.txt');
    }

    public function toDeleteFile(CliGuy $I)
    {
        $I->taskFileSystemStack()
            ->stopOnFail()
            ->remove('a.txt')
            ->run();
        $I->dontSeeFileFound('a.txt');
    }
}