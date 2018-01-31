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
        $classLoader->addPsr4('\\Robo\\PluginTest\\', [realpath(__DIR__.'/../../plugins')]);
        $service = new RelativeNamespaceDiscovery($classLoader);
        $service->setRelativeNamespace('Robo\Plugin');
        $classes = $service->getClasses();

        $this->assertContains('\Robo\PluginTest\Robo\Plugin\Commands\FirstCustomCommands', $classes);
        $this->assertContains('\Robo\PluginTest\Robo\Plugin\Commands\SecondCustomCommands', $classes);
    }

    public function testGetFile()
    {
        $classLoader = new ClassLoader();
        $classLoader->addPsr4('\\Robo\\PluginTest\\', [realpath(__DIR__.'/../../plugins')]);
        $service = new RelativeNamespaceDiscovery($classLoader);
        $service->setRelativeNamespace('Robo\Plugin');

        $actual = $service->getFile('\Robo\PluginTest\Robo\Plugin\Commands\FirstCustomCommands');
        $this->assertStringEndsWith($this->getPath('tests/plugins/Robo/Plugin/Commands/FirstCustomCommands.php'), $actual);

        $actual = $service->getFile('\Robo\PluginTest\Robo\Plugin\Commands\SecondCustomCommands');
        $this->assertStringEndsWith($this->getPath('tests/plugins/Robo/Plugin/Commands/SecondCustomCommands.php'), $actual);
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
          ['/A/B/C', $this->getPath('A\B\C')],
          ['A/B/C', $this->getPath('A\B\C')],
          ['A/B/C', $this->getPath('A\B\C')],
          ['A/B/C.php', $this->getPath('A\B\C')],
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
          ['A\B\C', $this->getPath('/A/B/C')],
          ['\A\B\C\\', $this->getPath('/A/B/C')],
          ['A\B\C\\', $this->getPath('/A/B/C')],
        ];
    }

    protected function callProtected($object, $method, $args = [])
    {
        $r = new \ReflectionMethod($object, $method);
        $r->setAccessible(true);
        return $r->invokeArgs($object, $args);
    }

    protected function getPath($path)
    {
        return str_replace('/', $this->ds, $path);
    }
}
