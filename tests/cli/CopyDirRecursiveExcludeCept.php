<?php
$I = new CliGuy($scenario);

$I->wantTo('copy dir recursively with CopyDir task, but exclude a file');
$I->amInPath(codecept_data_dir().'sandbox');
$I->seeDirFound('some/deeply/nested');
$I->seeDirFound('some/deeply/nested2');
$I->seeDirFound('some/deeply/nested3');
$I->seeDirFound('some/deeply/nested3/nested31');
$I->seeDirFound('some/deeply/nested4');
$I->seeDirFound('some/deeply/nested4/nested41');
$I->seeFileFound('structu.re', 'some/deeply/nested');
$I->seeFileFound('structu1.re', 'some/deeply/nested');
$I->seeFileFound('structu2.re', 'some/deeply/nested');
$I->seeFileFound('structu3.re', 'some/deeply/nested');
$I->seeFileFound('structu21.re', 'some/deeply/nested2');
$I->seeFileFound('structu31.re', 'some/deeply/nested3');
$I->seeFileFound('structu32.re', 'some/deeply/nested3');
$I->seeFileFound('structu311.re', 'some/deeply/nested3/nested31');
$I->seeFileFound('structu411.re', 'some/deeply/nested4/nested41');
$I->seeFileFound('structu412.re', 'some/deeply/nested4/nested41');
$I->taskCopyDir(['some/deeply' => 'some_destination/deeply'])
    ->exclude([
        // Basename exclusion.
        'structu1.re',
        // File in subdir exclusion.
        'some/deeply/nested/structu3.re',
        // Dir exclusion.
        'nested2',
        // Subdir exclusion.
        'some/deeply/nested3/nested31',
        // Subpath within source exclusion.
        'nested3/structu31.re',
        // File in deeper subpath within source exclusion.
        'nested4/nested41/structu411.re',
    ])
    ->run();
$I->seeDirFound('some_destination/deeply/nested');
$I->seeFileFound('structu.re', 'some_destination/deeply/nested');
$I->cantSeeFileFound('structu1.re', 'some_destination/deeply/nested');
$I->canSeeFileFound('structu2.re', 'some_destination/deeply/nested');
$I->cantSeeFileFound('structu3.re', 'some_destination/deeply/nested');
$I->cantSeeFileFound('nested2', 'some_destination/deeply');
$I->seeDirFound('some_destination/deeply/nested3');
$I->cantSeeFileFound('structu31.re', 'some_destination/deeply/nested3');
$I->canSeeFileFound('structu32.re', 'some_destination/deeply/nested3');
$I->cantSeeFileFound('nested31', 'some_destination/deeply/nested3');
$I->cantSeeFileFound('structu411.re', 'some_destination/deeply/nested4/nested41');
$I->canSeeFileFound('structu412.re', 'some_destination/deeply/nested4/nested41');
