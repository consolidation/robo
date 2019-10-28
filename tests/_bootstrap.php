<?php

// This is global bootstrap for autoloading
$kernel = \AspectMock\Kernel::getInstance();
$kernel->init([
    'debug' => true,
    'cacheDir' => '/tmp',
    'includePaths' => [
        __DIR__.'/../src',
        __DIR__.'/../vendor/symfony/process',
        __DIR__.'/../vendor/symfony/console',
        __DIR__.'/../vendor/henrikbjorn/lurker/src',
    ]
]);
