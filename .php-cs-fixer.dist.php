<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/examples',
    ])
    // Fixture files whose exact contents are used by the tests.
    ->exclude([
        '_data',
        '_log',
    ])
    ->append([
        __DIR__ . '/RoboFile.php',
        __DIR__ . '/robo',
        __DIR__ . '/.php-cs-fixer.dist.php',
    ]);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(false)
    ->setRules([
        '@PSR12' => true,
    ])
    ->setFinder($finder);
