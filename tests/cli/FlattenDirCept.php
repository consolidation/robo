<?php
$I = new CliGuy($scenario);
$I->getContainer()->addServiceProvider(\Robo\Task\FileSystem\ServiceProvider::class);

$I->wantTo('flatten dir with FlattenDir task');
$I->amInPath(codecept_data_dir().'sandbox');
$I->getContainer()->get('taskFlattenDir', [[
    'some/deeply/nested/*.re' => 'flattened',
    '*.txt' => 'flattened'
    ]])
    ->run();
$I->seeDirFound('flattened');
$I->seeFileFound('structu.re', 'flattened');
$I->seeFileFound('a.txt', 'flattened');
$I->seeFileFound('b.txt', 'flattened');
