<?php

namespace unit;

use Robo\Task\BaseTask;

class ConfigurationTest extends \Codeception\TestCase\Test
{
    public function testDifferentTasksCanHaveSameConfigKeys()
    {
        ConfigurationTestTaskA::configure('key', 'value-a');
        ConfigurationTestTaskB::configure('key', 'value-b');

        $taskA = new ConfigurationTestTaskA();
        verify($taskA->run())->equals('value-a');

        $taskB = new ConfigurationTestTaskB();
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
