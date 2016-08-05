<?php
$I = new CliGuy($scenario);
$I->getContainer()->addServiceProvider(\Robo\Task\Filesystem\loadTasks::getFilesystemServices());

$I->wantTo('copy dir with CopyDir task');
$I->amInPath(codecept_data_dir().'sandbox');
$I->taskCopyDir(['box' => 'bin'])
    ->run();
$I->seeDirFound('bin');
$I->seeFileFound('robo.txt', 'bin');

