<?php
$I = new CliGuy($scenario);
$I->getContainer()->addServiceProvider(\Robo\Task\FileSystem\ServiceProvider::class);

$I->wantTo('flatten dir with FlattenDir task including parents');
$I->amInPath(codecept_data_dir().'sandbox');
$I->getContainer()->get('taskFlattenDir', [['some/deeply/nested/*.re']])
    ->includeParents(array(1,1))
    ->parentDir('some')
    ->to('flattened')
    ->run();
$I->seeDirFound('flattened/deeply/nested');
$I->seeFileFound('structu.re', 'flattened/deeply/nested');
