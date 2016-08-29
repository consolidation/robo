<?php
$I = new CliGuy($scenario);

$I->wantTo('copy dir with CopyDir task');
$I->amInPath(codecept_data_dir().'sandbox');
$I->taskCopyDir(['box' => 'bin'])
    ->run();
$I->seeDirFound('bin');
$I->seeFileFound('robo.txt', 'bin');

