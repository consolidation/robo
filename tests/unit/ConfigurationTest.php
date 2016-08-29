<?php

namespace unit;

use Robo\Robo;
use Robo\Task\BaseTask;

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
