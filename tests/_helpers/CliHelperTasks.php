<?php
namespace Codeception\Module;

use Robo\Task\ValueProviderTask;

class CliHelperTasks extends \Robo\Tasks
{
    public function taskValueProvider()
    {
        return $this->task(ValueProviderTask::class);
    }
}
