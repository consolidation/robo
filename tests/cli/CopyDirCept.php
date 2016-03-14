<?php
$I = new CliGuy($scenario);
$I->getContainer()->addServiceProvider(\Robo\Task\FileSystem\ServiceProvider::class);

$I->wantTo('copy dir with CopyDir task');
$I->amInPath(codecept_data_dir().'sandbox');
$I->getContainer()->get('taskCopyDir', [['box' => 'bin']])
    ->run();
$I->seeDirFound('bin');
$I->seeFileFound('robo.txt', 'bin');
