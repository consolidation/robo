<?php

class WriteFileCest {

    public function _before(CliGuy $I)
    {
        $I->getContainer()->addServiceProvider(\Robo\Task\File\ServiceProvider::class);
        $I->amInPath(codecept_data_dir('sandbox'));
    }

    public function writeFewLines(CliGuy $I)
    {
        $I->wantTo('write lines with WriteToFile task');
        $I->task('WriteToFile', 'blogpost.md')
           ->line('****')
           ->line('hello world')
           ->line('****')
           ->run();
        $I->seeFileFound('blogpost.md');
        $I->seeFileContentsEqual(<<<HERE
****
hello world
****

HERE
        );
    }

    public function appendToFile(CliGuy $I)
    {
        $I->task('WriteToFile', 'a.txt')
           ->append()
           ->line('hello world')
           ->run();
        $I->seeFileFound('a.txt');
        $I->seeFileContentsEqual(<<<HERE
Ahello world

HERE
        );
    }

    public function insertFile(CliGuy $I)
    {
        $I->task('WriteToFile', 'a.txt')
            ->line('****')
            ->textFromFile('b.txt')
            ->line("C")
            ->run();
        $I->seeFileFound('a.txt');
        $I->seeFileContentsEqual(<<<HERE
****
BC

HERE
        );
    }

    public function replaceInFile(CliGuy $I)
    {
        $I->task('ReplaceInFile', 'a.txt')
            ->from('A')
            ->to('B')
            ->run();
        $I->seeFileFound('a.txt');
        $I->seeFileContentsEqual('B');

    }

    public function replaceMultipleInFile(CliGuy $I)
    {
        $I->task('ReplaceInFile', 'box/robo.txt')
            ->from(array('HELLO', 'ROBO'))
            ->to(array('Hello ', 'robo.li!'))
            ->run();
        $I->seeFileFound('box/robo.txt');
        $I->seeFileContentsEqual('Hello robo.li!');
    }
}

