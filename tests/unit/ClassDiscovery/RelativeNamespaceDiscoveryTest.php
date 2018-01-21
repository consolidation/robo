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
        $service = $this->getServiceInstance('\Commands');
        $service->getClassLoader()->addPsr4('\\Robo\\', [realpath(__DIR__.'/../../src')]);

        $classes = $service->getClasses();

        $this->assertContains('\Robo\Commands\FirstCustomCommands', $classes);
        $this->assertContains('\Robo\Commands\SecondCustomCommands', $classes);
    }

    public function testGetFile()
    {
        $service = $this->getServiceInstance('\Commands');
        $service->getClassLoader()->addPsr4('\\Robo\\', [realpath(__DIR__.'/../../src')]);

        $actual = $service->getFile('\Robo\Commands\FirstCustomCommands');
        $this->assertStringEndsWith('tests/src/Commands/FirstCustomCommands.php', $actual);

        $actual = $service->getFile('\Robo\Commands\SecondCustomCommands');
        $this->assertStringEndsWith('tests/src/Commands/SecondCustomCommands.php', $actual);
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

    /**
     * @param $relativeNamespace
     *
     * @return \Robo\ClassDiscovery\RelativeNamespaceDiscovery
     */
    protected function getServiceInstance($relativeNamespace)
    {
        return (new RelativeNamespaceDiscovery())
            ->setRelativeNamespace($relativeNamespace)
            ->setClassLoader(new ClassLoader());
    }

    protected function callProtected($object, $method, $args = [])
    {
        $r = new \ReflectionMethod($object, $method);
        $r->setAccessible(true);
        return $r->invokeArgs($object, $args);
    }
}
