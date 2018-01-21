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

    /**
     * @dataProvider testConvertPathToNamespaceData
     *
     * @param $path
     * @param $expected
     */
    public function testConvertPathToNamespace($path, $expected)
    {
        $discovery = new RelativeNamespaceDiscovery('');
        $actual = $this->callProtected($discovery, 'convertPathToNamespace', [$path]);
        $this->assertEquals($expected, $actual);
    }

    public function testConvertPathToNamespaceData()
    {
        return [
          ['/A/B/C', 'A\B\C'],
          ['A/B/C', 'A\B\C'],
          ['A/B/C', 'A\B\C'],
          ['A/B/C.php', 'A\B\C'],
        ];
    }

    /**
     * @dataProvider testConvertNamespaceToPathData
     *
     * @param $namespace
     * @param $expected
     */
    public function testConvertNamespaceToPath($namespace, $expected)
    {
        $discovery = new RelativeNamespaceDiscovery('');
        $actual = $this->callProtected($discovery, 'convertNamespaceToPath', [$namespace]);
        $this->assertEquals($expected, $actual);
    }

    public function testConvertNamespaceToPathData()
    {
        return [
          ['A\B\C', '/A/B/C'],
          ['\A\B\C\\', '/A/B/C'],
          ['A\B\C\\', '/A/B/C'],
        ];
    }

    function callProtected($object, $method, $args = [])
    {
        $r = new \ReflectionMethod($object, $method);
        $r->setAccessible(true);
        return $r->invokeArgs($object, $args);
    }
}