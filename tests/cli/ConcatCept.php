<?php
$I = new CliGuy($scenario);
$I->getContainer()->addServiceProvider(\Robo\Task\File\loadTasks::getFileServices());

$I->wantTo('concat files using Concat Task');
$I->amInPath(codecept_data_dir() . 'sandbox');
$I->taskConcat(['a.txt', 'b.txt'])
    ->to('merged.txt')
    ->run();
$I->seeFileFound('merged.txt');
$I->seeFileContentsEqual("A\nB\n");

