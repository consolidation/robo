<?php
$I = new CliGuy($scenario);
$I->getContainer()->addServiceProvider(\Robo\Task\FileSystem\ServiceProvider::class);

$I->wantTo('clean dir with DeleteDirTask');
$I->amInPath(codecept_data_dir());
$I->seeFileFound('robo.txt', 'sandbox');
$I->taskCleanDir(['sandbox'])
    ->run();
$I->dontSeeFileFound('box', 'sandbox');
$I->dontSeeFileFound('robo.txt', 'sandbox');
$I->dontSeeFileFound('a.txt' , 'sandbox');
