<?php

namespace Robo\Task;

use Robo\Task\ValueProviderTask;

trait TestHelperTasks
{
    public function taskValueProvider()
    {
        return $this->task(ValueProviderTask::class);
    }
}
