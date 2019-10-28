<?php

namespace unit;

use Robo\Robo;
use Robo\Task\BaseTask;
use Consolidation\Config\Loader\ConfigProcessor;
use Consolidation\Config\Loader\YamlConfigLoader;

class ConfigurationTest extends \Codeception\TestCase\Test
{
    public function testDifferentTasksCanHaveSameConfigKeys()
    {
        ConfigurationTestTaskA::configure('key', 'value-a');
        ConfigurationTestTaskB::configure('key', 'value-b');

        $taskA = new ConfigurationTestTaskA();
        $taskA->setConfig(Robo::config());
        $this->assertEquals(
            'value-a',
            $taskA->run());

        $taskB = new ConfigurationTestTaskB();
        $taskB->setConfig(Robo::config());
        $this->assertEquals(
            'value-b',
            $taskB->run());
    }

    public function testConfigurationWithCrossFileReferences()
    {
        $processor = new ConfigProcessor();
        $loader = new YamlConfigLoader();
        $processor->extend($loader->load(dirname(__DIR__) . '/_data/config-1.yml'));
        $processor->extend($loader->load(dirname(__DIR__) . '/_data/config-2.yml'));
        $processor->extend($loader->load(dirname(__DIR__) . '/_data/config-3.yml'));

        $sources = $processor->sources();
        $this->assertEquals(
            dirname(__DIR__) . '/_data/config-3.yml',
            $sources['a']);
        $this->assertEquals(
            dirname(__DIR__) . '/_data/config-2.yml',
            $sources['b']);
        $this->assertEquals(
            dirname(__DIR__) . '/_data/config-1.yml',
            $sources['c']);

        \Robo\Robo::config()->import($processor->export());

        $this->assertEquals(
            '3',
            implode(',', \Robo\Robo::config()->get('m')));
        $this->assertEquals(
            'foobarbaz',
            \Robo\Robo::config()->get('a'));
    }

    public function testConfigurationWithReverseOrderCrossFileReferences()
    {
        $processor = new ConfigProcessor();
        $loader = new YamlConfigLoader();
        $processor->extend($loader->load(dirname(__DIR__) . '/_data/config-3.yml'));
        $processor->extend($loader->load(dirname(__DIR__) . '/_data/config-2.yml'));
        $processor->extend($loader->load(dirname(__DIR__) . '/_data/config-1.yml'));

        $sources = $processor->sources();
        $this->assertEquals(
            dirname(__DIR__) . '/_data/config-3.yml',
            $sources['a']);
        $this->assertEquals(
            dirname(__DIR__) . '/_data/config-2.yml',
            $sources['b']);
        $this->assertEquals(
            dirname(__DIR__) . '/_data/config-1.yml',
            $sources['c']);

        \Robo\Robo::config()->import($processor->export());

        $this->assertEquals(
            '1',
            implode(',', \Robo\Robo::config()->get('m')));

        if (strpos(\Robo\Robo::config()->get('a'), '$') !== false) {
            throw new \PHPUnit_Framework_SkippedTestError(
                'Evaluation of cross-file references in reverse order not supported.'
            );
        }
        $this->assertEquals(
            'foobarbaz',
            \Robo\Robo::config()->get('a'));
    }
}

class ConfigurationTestTaskA extends BaseTask
{
    public function run()
    {
        return $this->getConfigValue('key');
    }
}

class ConfigurationTestTaskB extends BaseTask
{
    public function run()
    {
        return $this->getConfigValue('key');
    }
}
