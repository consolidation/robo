<?php
$I = new CliGuy($scenario);
$I->getContainer()->addServiceProvider(\Robo\Task\Filesystem\loadTasks::getFilesystemServices());

$I->wantTo('flatten dir with FlattenDir task');
$I->amInPath(codecept_data_dir().'sandbox');
$I->taskFlattenDir([
    'some/deeply/nested/*.re' => 'flattened',
    '*.txt' => 'flattened'
    ])
    ->run();
$I->seeDirFound('flattened');
$I->seeFileFound('structu.re', 'flattened');
$I->seeFileFound('a.txt', 'flattened');
$I->seeFileFound('b.txt', 'flattened');
