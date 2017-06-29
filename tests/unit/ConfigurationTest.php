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
        verify($taskA->run())->equals('value-a');

        $taskB = new ConfigurationTestTaskB();
        $taskB->setConfig(Robo::config());
        verify($taskB->run())->equals('value-b');
    }

    public function testConfigurationWithCrossFileReferences()
    {
        $processor = new ConfigProcessor();
        $loader = new YamlConfigLoader();
        $processor->extend($loader->load(dirname(__DIR__) . '/_data/config-1.yml'));
        $processor->extend($loader->load(dirname(__DIR__) . '/_data/config-2.yml'));
        $processor->extend($loader->load(dirname(__DIR__) . '/_data/config-3.yml'));

        $sources = $processor->sources();
        verify($sources['a'])->equals(dirname(__DIR__) . '/_data/config-3.yml');
        verify($sources['b'])->equals(dirname(__DIR__) . '/_data/config-2.yml');
        verify($sources['c'])->equals(dirname(__DIR__) . '/_data/config-1.yml');

        \Robo\Robo::config()->import($processor->export());

        verify(implode(',', \Robo\Robo::config()->get('m')))->equals('3');
        verify(\Robo\Robo::config()->get('a'))->equals('foobarbaz');
    }

    public function testConfigurationWithReverseOrderCrossFileReferences()
    {
        $processor = new ConfigProcessor();
        $loader = new YamlConfigLoader();
        $processor->extend($loader->load(dirname(__DIR__) . '/_data/config-3.yml'));
        $processor->extend($loader->load(dirname(__DIR__) . '/_data/config-2.yml'));
        $processor->extend($loader->load(dirname(__DIR__) . '/_data/config-1.yml'));

        $sources = $processor->sources();
        verify($sources['a'])->equals(dirname(__DIR__) . '/_data/config-3.yml');
        verify($sources['b'])->equals(dirname(__DIR__) . '/_data/config-2.yml');
        verify($sources['c'])->equals(dirname(__DIR__) . '/_data/config-1.yml');

        \Robo\Robo::config()->import($processor->export());

        verify(implode(',', \Robo\Robo::config()->get('m')))->equals('1');

        if (strpos(\Robo\Robo::config()->get('a'), '$') !== false) {
            throw new \PHPUnit_Framework_SkippedTestError(
                'Evaluation of cross-file references in reverse order not supported.'
            );
        }
        verify(\Robo\Robo::config()->get('a'))->equals('foobarbaz');
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
