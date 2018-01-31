<?php

use Robo\ClassDiscovery\RelativeNamespaceDiscovery;
use Composer\Autoload\ClassLoader;

/**
 * Class RelativeNamespaceDiscoveryTest
 */
class RelativeNamespaceDiscoveryTest extends \Codeception\Test\Unit
{
    private $ds = DIRECTORY_SEPARATOR;

    public function testGetClasses()
    {
        $classLoader = new ClassLoader();
        $classLoader->addPsr4('\\Robo\\', [realpath(__DIR__.'/../../src')]);
        $service = new RelativeNamespaceDiscovery($classLoader);
        $service->setRelativeNamespace('\Commands');
        $classes = $service->getClasses();

        $this->assertContains('\Robo\Commands\FirstCustomCommands', $classes);
        $this->assertContains('\Robo\Commands\SecondCustomCommands', $classes);
    }

    public function testGetFile()
    {
        $classLoader = new ClassLoader();
        $classLoader->addPsr4('\\Robo\\', [realpath(__DIR__.'/../../src')]);
        $service = new RelativeNamespaceDiscovery($classLoader);
        $service->setRelativeNamespace('\Commands');

        $actual = $service->getFile('\Robo\Commands\FirstCustomCommands');
        $this->assertStringEndsWith($this->normalizePath('tests/src/Commands/FirstCustomCommands.php'), $actual);

        $actual = $service->getFile('\Robo\Commands\SecondCustomCommands');
        $this->assertStringEndsWith($this->normalizePath('tests/src/Commands/SecondCustomCommands.php'), $actual);
    }

    /**
     * @dataProvider testConvertPathToNamespaceData
     *
     * @param $path
     * @param $expected
     */
    public function testConvertPathToNamespace($path, $expected)
    {
        $classLoader = new ClassLoader();
        $discovery = new RelativeNamespaceDiscovery($classLoader);
        $actual = $this->callProtected($discovery, 'convertPathToNamespace', [$path]);
        $this->assertEquals($expected, $actual);
    }

    public function testConvertPathToNamespaceData()
    {
        return [
          ['/A/B/C', $this->normalizePath('A\B\C')],
          ['A/B/C', $this->normalizePath('A\B\C')],
          ['A/B/C', $this->normalizePath('A\B\C')],
          ['A/B/C.php', $this->normalizePath('A\B\C')],
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
        $classLoader = new ClassLoader();
        $discovery = new RelativeNamespaceDiscovery($classLoader);
        $actual = $this->callProtected($discovery, 'convertNamespaceToPath', [$namespace]);
        $this->assertEquals($expected, $actual);
    }

    public function testConvertNamespaceToPathData()
    {
        return [
          ['A\B\C', $this->normalizePath('/A/B/C')],
          ['\A\B\C\\', $this->normalizePath('/A/B/C')],
          ['A\B\C\\', $this->normalizePath('/A/B/C')],
        ];
    }

    protected function callProtected($object, $method, $args = [])
    {
        $r = new \ReflectionMethod($object, $method);
        $r->setAccessible(true);
        return $r->invokeArgs($object, $args);
    }

    protected function normalizePath($path)
    {
        return str_replace('/', $this->ds, $path);
    }
}
