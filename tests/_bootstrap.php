<?php
use Robo\Config;

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

// Default container will do for unit tests.
// Might want to change this in the future.
Config::setContainer(Config::createContainer());
