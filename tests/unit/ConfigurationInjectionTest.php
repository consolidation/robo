<?php
use Robo\Robo;

use Consolidation\Config\Loader\ConfigProcessor;
use Consolidation\Config\Loader\YamlConfigLoader;

class ConfigurationInjectionTest extends \Codeception\TestCase\Test
{
    /**
     * @var \Robo\Runner
     */
    private $runner;

    /**
     * @var \CodeGuy
     */
    protected $guy;

    public function _before()
    {
        $this->runner = new \Robo\Runner('\Robo\RoboFileFixture');
    }

    public function testNoOptionsNoConfiguration()
    {
        // Run without any config and without any options
        $argv = ['placeholder', 'test:simple-list'];
        $result = $this->runner->execute($argv, null, null, $this->guy->capturedOutputStream());

        $this->guy->seeInOutput("a: '1'");
        $this->guy->seeInOutput("b: '2'");
    }

    public function testOptionsButNoConfiguration()
    {
        // Set one option, but provide no config
        $argv = ['placeholder', 'test:simple-list', '--b=3'];
        $result = $this->runner->execute($argv, null, null, $this->guy->capturedOutputStream());

        $this->guy->seeInOutput("a: '1'");
        $this->guy->seeInOutput("b: '3'");
    }

    public function testWithConfigurationButNoOptions()
    {
        \Robo\Robo::config()->set('command.test.simple-list.options.a', '4');
        \Robo\Robo::config()->set('command.test.simple-list.options.b', '5');

        $argv = ['placeholder', 'test:simple-list'];
        $result = $this->runner->execute($argv, null, null, $this->guy->capturedOutputStream());

        $this->guy->seeInOutput("a: '4'");
        $this->guy->seeInOutput("b: '5'");
    }

    public function testHelpWithoutConfiguration()
    {
        $argv = ['placeholder', 'help', 'test:simple-list'];
        $result = $this->runner->execute($argv, null, null, $this->guy->capturedOutputStream());

        $this->guy->seeInOutput('[default: "1"]');
        $this->guy->seeInOutput('[default: "2"]');
    }

    public function testHelpWithConfigurationButNoOptions()
    {
        \Robo\Robo::config()->set('command.test.simple-list.options.a', '4');
        \Robo\Robo::config()->set('command.test.simple-list.options.b', '5');

        $argv = ['placeholder', 'help', 'test:simple-list'];
        $result = $this->runner->execute($argv, null, null, $this->guy->capturedOutputStream());

        $this->guy->seeInOutput('[default: "4"]');
        $this->guy->seeInOutput('[default: "5"]');
    }

    public function testWithConfigurationAndOptionOverride()
    {
        \Robo\Robo::config()->set('command.test.simple-list.options.a', '4');
        \Robo\Robo::config()->set('command.test.simple-list.options.b', '5');

        $argv = ['placeholder', 'test:simple-list', '--b=6'];
        $result = $this->runner->execute($argv, null, null, $this->guy->capturedOutputStream());

        $this->guy->seeInOutput("a: '4'");
        $this->guy->seeInOutput("b: '6'");
    }

    public function testWithConfigurationFallbacks()
    {
        \Robo\Robo::config()->set('command.test.simple-list.options.a', '4');
        \Robo\Robo::config()->set('command.test.options.b', '7');

        $argv = ['placeholder', 'test:simple-list'];
        $result = $this->runner->execute($argv, null, null, $this->guy->capturedOutputStream());

        $this->guy->seeInOutput("a: '4'");
        $this->guy->seeInOutput("b: '7'");
    }

    public function testSettingConfigurationFromCommandOptions()
    {
        $argv = ['placeholder', 'test:simple-list', '-D', 'config.key=value'];
        $result = $this->runner->execute($argv, null, null, $this->guy->capturedOutputStream());

        $this->guy->seeInOutput("a: '1'");
        $this->guy->seeInOutput("b: '2'");

        $actual = \Robo\Robo::config()->get('config.key');
        $this->assertEquals('value', $actual);
    }

    public function testWithConfigLoader()
    {
        $loader = new YamlConfigLoader();
        $loader->load(dirname(__DIR__) . '/_data/robo.yml');

        \Robo\Robo::config()->import($loader->export());

        $argv = ['placeholder', 'test:simple-list'];
        $result = $this->runner->execute($argv, null, null, $this->guy->capturedOutputStream());

        $this->guy->seeInOutput("a: '12'");
        $this->guy->seeInOutput("b: '13'");
    }

    public function testWithConfigLoaderAndCliOverride()
    {
        $loader = new YamlConfigLoader();
        $loader->load(dirname(__DIR__) . '/_data/robo.yml');

        \Robo\Robo::config()->import($loader->export());

        $argv = ['placeholder', 'test:simple-list', '--b=3'];
        $result = $this->runner->execute($argv, null, null, $this->guy->capturedOutputStream());

        $this->guy->seeInOutput("a: '12'");
        $this->guy->seeInOutput("b: '3'");
    }

    public function testWithConfigProcessor()
    {
        $processor = new ConfigProcessor();
        $loader = new YamlConfigLoader();
        $processor->extend($loader->load(dirname(__DIR__) . '/_data/robo.yml'));
        $processor->extend($loader->load(dirname(__DIR__) . '/_data/robo2.yml'));
        \Robo\Robo::config()->import($processor->export());

        $argv = ['placeholder', 'test:simple-list'];
        $result = $this->runner->execute($argv, null, null, $this->guy->capturedOutputStream());

        $this->guy->seeInOutput("a: '112'");
        $this->guy->seeInOutput("b: '13'");
    }

    public function testCommandWithTaskConfiguration()
    {
        $loader = new YamlConfigLoader();
        $loader->load(dirname(__DIR__) . '/_data/robo.yml');

        \Robo\Robo::config()->import($loader->export());

        $argv = ['placeholder', 'test:exec', '--simulate'];
        $result = $this->runner->execute($argv, null, null, $this->guy->capturedOutputStream());

        // `task.Base.Exec.settings.dir` is defined in loaded robo.yml configuration file.
        $this->guy->seeInOutput("->dir('/some/dir')");
    }

    public function testCommandWithFallbackTaskConfiguration()
    {
        $loader = new YamlConfigLoader();
        $loader->load(dirname(__DIR__) . '/_data/falback-task-config-robo.yml');

        \Robo\Robo::config()->import($loader->export());

        $argv = ['placeholder', 'test:exec', '--simulate'];
        $result = $this->runner->execute($argv, null, null, $this->guy->capturedOutputStream());

        // `task.Base.settings.dir` is defined in loaded robo.yml configuration file.
        $this->guy->seeInOutput("->dir('/some/other/dir')");
    }
}
