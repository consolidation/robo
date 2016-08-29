<?php
$I = new CliGuy($scenario);

$I->wantTo('copy dir recursively with CopyDir task');
$I->amInPath(codecept_data_dir() . 'sandbox');
$I->seeDirFound('some/deeply/nested');
$I->seeFileFound('structu.re', 'some/deeply/nested');
$I->taskCopyDir(['some/deeply' => 'some_destination/deeply'])
    ->run();
$I->seeDirFound('some_destination/deeply/nested');
$I->seeFileFound('structu.re', 'some_destination/deeply/nested');
