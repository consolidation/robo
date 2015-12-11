<?php
$I = new CliGuy($scenario);
$I->wantTo('archive directory and then extract it again with Archive and Extract tasks');
$I->amInPath(codecept_data_dir() . 'sandbox');
$I->seeDirFound('some/deeply/nested');
$I->seeFileFound('structu.re', 'some/deeply/nested');
$I->taskArchive('some/deeply.tgz')
    ->add(['deeply' => 'some/deeply'])
    ->run();
$I->seeFileFound('deeply.tgz', 'some');
$I->taskExtract('some/deeply.tgz')
    ->to('extracted')
    ->run();
$I->seeDirFound('extracted/nested');
$I->seeFileFound('structu.re', 'extracted/nested');
