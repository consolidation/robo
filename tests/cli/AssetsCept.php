<?php

$I = new CliGuy($scenario);

$I->wantTo('minify a css file');
$I->amInPath(codecept_data_dir().'sandbox');

$sampleCss = dirname(__DIR__) . '/_data/sample.css';
$outputCss = 'minifiedSample.css';

$initialFileSize = filesize($sampleCss);

$I->taskMinify($sampleCss)
  ->to('minifiedSample.css')
  ->run();

$I->seeFileFound($outputCss);
$minifiedFileSize = filesize($outputCss);
$outputCssContents = file_get_contents($outputCss);

$I->assertLessThan($initialFileSize, $minifiedFileSize, 'Minified file is smaller than the source file');
$I->assertGreaterThan(0, $minifiedFileSize, 'Minified file is not empty');
$I->assertContains('body', $outputCssContents, 'Minified file has some content from the source file');
$I->assertNotContains('Sample css file', $outputCssContents, 'Minified file does not contain comment from source file');
