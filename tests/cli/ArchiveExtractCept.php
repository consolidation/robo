<?php
$I = new CliGuy($scenario);
$I->wantTo('archive directory and then extract it again with Archive and Extract tasks');
$I->amInPath(codecept_data_dir() . 'sandbox');
$I->seeDirFound('some/deeply/nested');
$I->seeFileFound('structu.re', 'some/deeply/nested');

// Test a bunch of archive types that zippy supports
foreach (['zip'/*, 'tar', 'tar.gz', 'tar.bz2', 'tgz'*/] as $archiveType) {
  $I->taskArchive("deeply.$archiveType")
      ->add(['deeply' => 'some/deeply'])
      ->run();
  $I->seeFileFound("deeply.$archiveType");
  $I->taskExtract("deeply.$archiveType")
      ->to("extracted-$archiveType")
      ->run();
  $I->seeDirFound("extracted-$archiveType/nested");
  $I->seeFileFound('structu.re', "extracted-$archiveType/nested");
}
