<?php
namespace Robo\Task;

use Robo\Result;
use Robo\Task\BaseTask;
use Robo\Collection\Collection;

class CollectionTestTask extends BaseTask
{
    protected $key;
    protected $value;

    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function run()
    {
        return $this->getValue();
    }

    protected function getValue()
    {
        $result = Result::success($this);
        $result[$this->key] = $this->value;

        return $result;
    }

    // Note that by returning a value with the same
    // key as the result, we overwrite the value generated
    // by the primary task method ('run()').  If we returned
    // a result with a different key, then both values
    // would appear in the result.
    public function parenthesizer()
    {
        $this->value = "({$this->value})";
        return $this->getValue();
    }

    public function emphasizer()
    {
        $this->value = "*{$this->value}*";
        return $this->getValue();
    }
}

