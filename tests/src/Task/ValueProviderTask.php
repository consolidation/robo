<?php

namespace Robo\Task;

use Robo\Result;
use Robo\Task\BaseTask;
use Robo\Collection\Collection;

class ValueProviderTask extends BaseTask
{
    protected $message = '';
    protected $data = [];

    public function run()
    {
        return Result::success($this, $this->message, $this->data);
    }

    public function provideData($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function provideItem($value)
    {
        return $this->provideData('item', $value);
    }

    public function provideMessage($message)
    {
        $this->message = $message;
        return $this;
    }
}
