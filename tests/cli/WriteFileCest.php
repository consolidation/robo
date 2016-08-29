<?php

class WriteFileCest {

    public function _before(CliGuy $I)
    {
        $I->amInPath(codecept_data_dir('sandbox'));
    }

    public function writeFewLines(CliGuy $I)
    {
        $I->wantTo('write lines with WriteToFile task');
        $I->taskWriteToFile('blogpost.md')
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
        $I->taskWriteToFile('a.txt')
           ->append()
           ->line('hello world')
           ->run();
        $I->seeFileFound('a.txt');
        $I->seeFileContentsEqual(<<<HERE
Ahello world

HERE
        );
    }

    public function testWouldChange(CliGuy $I)
    {
        $writeTask = $I->taskWriteToFile('a.txt')
           ->append();
        $I->assertEquals(false, $writeTask->wouldChange(), "No changes to test file.");
        $writeTask->line('hello world');
        $I->assertEquals(true, $writeTask->wouldChange(), "Test file would change.");
    }

    public function insertFile(CliGuy $I)
    {
        $I->taskWriteToFile('a.txt')
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

    public function appendIfMatch(CliGuy $I)
    {
        $I->wantTo('append lines with WriteToFile task, but only if pattern does not match');
        $I->taskWriteToFile('blogpost.md')
           ->line('****')
           ->line('hello world')
           ->line('****')
           ->appendUnlessMatches('/hello/', 'Should not add this')
           ->appendUnlessMatches('/goodbye/', 'Should add this')
           ->appendIfMatches('/hello/', ' and should also add this')
           ->appendIfMatches('/goodbye/', ' but should not add this')
           ->appendIfMatches('/should/', '!')
           ->run();
        $I->seeFileFound('blogpost.md');
        $I->seeFileContentsEqual(<<<HERE
****
hello world
****
Should add this and should also add this!
HERE
        );
    }

    public function replaceInFile(CliGuy $I)
    {
        $I->taskReplaceInFile('a.txt')
            ->from('A')
            ->to('B')
            ->run();
        $I->seeFileFound('a.txt');
        $I->seeFileContentsEqual('B');

    }

    public function replaceMultipleInFile(CliGuy $I)
    {
        $I->taskReplaceInFile('box/robo.txt')
            ->from(array('HELLO', 'ROBO'))
            ->to(array('Hello ', 'robo.li!'))
            ->run();
        $I->seeFileFound('box/robo.txt');
        $I->seeFileContentsEqual('Hello robo.li!');
    }
}

