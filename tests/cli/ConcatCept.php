<?php
$I = new CliGuy($scenario);
$I->getContainer()->addServiceProvider(\Robo\Task\File\ServiceProvider::class);

$I->wantTo('concat files using Concat Task');
$I->amInPath(codecept_data_dir() . 'sandbox');
$I->getContainer()->get('taskConcat', [['a.txt', 'b.txt']])
    ->to('merged.txt')
    ->run();
$I->seeFileFound('merged.txt');
$I->seeFileContentsEqual("A\nB\n");
