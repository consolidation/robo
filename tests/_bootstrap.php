<?php

// This is global bootstrap for autoloading
$kernel = \AspectMock\Kernel::getInstance();
$kernel->init([
    'debug' => true,
    'includePaths' => [
        __DIR__.'/../src',
        __DIR__.'/../vendor/symfony/process',
        __DIR__.'/../vendor/symfony/console',
    ]
]);
