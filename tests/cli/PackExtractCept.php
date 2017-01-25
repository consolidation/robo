<?php

$I = new CliGuy($scenario);

$I->wantTo('archive directory and then extract it again with Archive and Extract tasks');
$I->amInPath(codecept_data_dir().'sandbox');
$I->seeDirFound('some/deeply/nested');
$I->seeFileFound('structu.re', 'some/deeply/nested');
$I->seeFileFound('existing_file', 'some/deeply');

$linuxSupportedTypes = ['zip', 'tar', 'tar.gz', 'tar.bz2', 'tgz'];

$windowsSupportedTypes = ['zip'];

$supportedTypes = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? $windowsSupportedTypes : $linuxSupportedTypes;

// Test a bunch of archive types that we support
foreach ($supportedTypes as $archiveType) {
    // First, take everything from the folder 'some/deeply' and make
    // an archive for it located in 'deep'
    $I->taskPack("deeply.$archiveType")
        ->add(['deep' => 'some/deeply'])
        ->run();
    $I->seeFileFound("deeply.$archiveType");
    // We are next going to extract the archive we created, this time
    // putting it into a folder called "extracted-$archiveType" (different
    // for each archive type we test).  We rely on the default behavior
    // of our extractor to remove the top-level directory in the archive
    // ("deeply").
    $I->taskExtract("deeply.$archiveType")
        ->to("extracted-$archiveType")
        ->preserveTopDirectory(false) // this is the default
        ->run();
    $I->seeDirFound("extracted-$archiveType");
    $I->seeDirFound("extracted-$archiveType/nested");
    $I->seeFileFound('structu.re', "extracted-$archiveType/nested");
    // Next, we'll extract the same archive again, this time preserving
    // the top-level folder.
    $I->taskExtract("deeply.$archiveType")
        ->to("preserved-$archiveType")
        ->preserveTopDirectory()
        ->run();
    $I->seeDirFound("preserved-$archiveType");
    $I->seeDirFound("preserved-$archiveType/deep/nested");
    $I->seeFileFound('structu.re', "preserved-$archiveType/deep/nested");
    // Make another archive, this time composed of fanciful locations
    $I->taskPack("composed.$archiveType")
        ->add(['a/b/existing_file' => 'some/deeply/existing_file'])
        ->add(['x/y/z/structu.re' => 'some/deeply/nested/structu.re'])
        ->run();
    $I->seeFileFound("composed.$archiveType");
    // Extract our composed archive, and see if the resulting file
    // structure matches expectations.
    $I->taskExtract("composed.$archiveType")
        ->to("decomposed-$archiveType")
        ->preserveTopDirectory()
        ->run();
    $I->seeDirFound("decomposed-$archiveType");
    $I->seeDirFound("decomposed-$archiveType/x/y/z");
    $I->seeFileFound('structu.re', "decomposed-$archiveType/x/y/z");
    $I->seeDirFound("decomposed-$archiveType/a/b");
    $I->seeFileFound('existing_file', "decomposed-$archiveType/a/b");
}
