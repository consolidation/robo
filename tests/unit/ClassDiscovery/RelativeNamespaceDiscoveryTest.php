<?php

use Robo\ClassDiscovery\RelativeNamespaceDiscovery;
use Composer\Autoload\ClassLoader;

/**
 * Class RelativeNamespaceDiscoveryTest
 */
class RelativeNamespaceDiscoveryTest extends \Codeception\Test\Unit
{
    public function testGetClasses()
    {
        $classLoader = new ClassLoader();
        $classLoader->addPsr4('\\Robo\\', [realpath(__DIR__.'/../../src')]);

        $discovery = new RelativeNamespaceDiscovery('\Commands');
        $discovery->setClassLoader($classLoader);

        $classes = $discovery->getClasses();

        $this->assertContains('\Robo\FirstCustomCommands', $classes);
        $this->assertContains('\Robo\SecondCustomCommands', $classes);
    }
}