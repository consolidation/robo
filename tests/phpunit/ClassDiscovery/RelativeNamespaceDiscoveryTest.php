<?php

use PHPUnit\Framework\TestCase;
use Robo\ClassDiscovery\RelativeNamespaceDiscovery;
use Composer\Autoload\ClassLoader;

/**
 * Class RelativeNamespaceDiscoveryTest
 */
class RelativeNamespaceDiscoveryTest extends TestCase
{
    public function testGetClasses()
    {
        $classLoader = new ClassLoader();
        $classLoader->addPsr4('\\Robo\\PluginTest\\', [realpath(__DIR__.'/../../plugins')]);
        $service = new RelativeNamespaceDiscovery($classLoader);
        $service->setRelativeNamespace('Robo\Plugin');
        $service->setSearchPattern('/.*Commands?\.php$/');
        $classes = $service->getClasses();

        $this->assertContains('\Robo\PluginTest\Robo\Plugin\Commands\FirstCustomCommands', $classes);
        $this->assertContains('\Robo\PluginTest\Robo\Plugin\Commands\SecondCustomCommands', $classes);
        $this->assertContains('\Robo\PluginTest\Robo\Plugin\Commands\ThirdCustomCommand', $classes);
        $this->assertNotContains('\Robo\PluginTest\Robo\Plugin\Commands\NotValidClassName', $classes);
    }

    public function testGetFile()
    {
        $classLoader = new ClassLoader();
        $classLoader->addPsr4('\\Robo\\PluginTest\\', [realpath(__DIR__.'/../../plugins')]);
        $service = new RelativeNamespaceDiscovery($classLoader);
        $service->setRelativeNamespace('Robo\Plugin');

        $actual = $service->getFile('\Robo\PluginTest\Robo\Plugin\Commands\FirstCustomCommands');
        $this->assertStringEndsWith('FirstCustomCommands.php', $actual);

        $actual = $service->getFile('\Robo\PluginTest\Robo\Plugin\Commands\SecondCustomCommands');
        $this->assertStringEndsWith('SecondCustomCommands.php', $actual);


        $actual = $service->getFile('\Robo\PluginTest\Robo\Plugin\Commands\ThirdCustomCommand');
        $this->assertStringEndsWith('ThirdCustomCommand.php', $actual);
    }

    /**
     * @dataProvider convertPathToNamespaceData
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

    public function convertPathToNamespaceData()
    {
        return [
          ['/A/B/C', 'A\B\C'],
          ['A/B/C', 'A\B\C'],
          ['A/B/C', 'A\B\C'],
          ['A/B/C.php', 'A\B\C'],
        ];
    }

    /**
     * @dataProvider convertNamespaceToPathData
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

    public function convertNamespaceToPathData()
    {
        return [
          ['A\B\C', '/A/B/C'],
          ['\A\B\C\\', '/A/B/C'],
          ['A\B\C\\', '/A/B/C'],
        ];
    }

    protected function callProtected($object, $method, $args = [])
    {
        $r = new \ReflectionMethod($object, $method);
        $r->setAccessible(true);
        return $r->invokeArgs($object, $args);
    }
}
