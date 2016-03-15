<?php
$I = new CliGuy($scenario);
$I->getContainer()->addServiceProvider(\Robo\Task\FileSystem\ServiceProvider::class);

$I->wantTo('overwrite a file with CopyDir task');
$I->amInPath(codecept_data_dir() . 'sandbox');
$I->seeDirFound('some');
$I->seeFileFound('existing_file', 'some');
$I->task('CopyDir', ['some' => 'some_destination'])
    ->run();
$I->seeFileFound('existing_file', 'some_destination/deeply');
$I->openFile('some_destination/deeply/existing_file');
$I->seeInThisFile('some existing file');
