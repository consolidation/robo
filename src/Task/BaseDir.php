<?php
namespace Robo\Task;
use Robo\Add\Output;
use Robo\TaskInterface;

abstract class BaseDir implements TaskInterface {
    use Output;

    protected $dirs = [];

    public function __construct($dirs)
    {
        is_array($dirs)
            ? $this->dirs = $dirs
            : $this->dirs[] = $dirs;
    }

}
 