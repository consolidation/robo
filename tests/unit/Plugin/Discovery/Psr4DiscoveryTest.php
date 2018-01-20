<?php

/**
 * Class Psr4Discovery
 */
class Psr4DiscoveryTest extends \Codeception\Test\Unit
{

    public function testDiscovery()
    {
        $classLoader = new \Composer\Autoload\ClassLoader();
        $classLoader->addPsr4('\\Robo\\', [realpath(__DIR__.'/../../../src')]);

        $discovery = new \Robo\Plugin\Discovery\Psr4Discovery('\Commands');
        $discovery->setClassLoader($classLoader);

        $definitions = $discovery->getDefinitions();
    }
}