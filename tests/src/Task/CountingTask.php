<?php
namespace Robo\Task;

use Robo\Result;
use Robo\Task\BaseTask;
use Robo\Collection\Collection;

class CountingTask extends BaseTask
{
    protected $count = 0;

    public function run()
    {
        $this->count++;
        return Result::success($this);
    }

    public function getCount()
    {
        return $this->count;
    }
}
