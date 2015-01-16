<?php

namespace Robo\Task\Gulp;

use Robo\Task\BaseTask;
use Robo\Exception\TaskException;

abstract class Base extends BaseTask
{
    use \Robo\Common\ExecOneCommand;

    protected $opts = [];
    protected $task = '';


    /**
     * adds `silent` option to gulp
     *
     * @return $this
     */
    public function silent()
    {
        $this->option('silent');
        return $this;
    }

    /**
     * adds `--no-color` option to gulp
     *
     * @return $this
     */
    public function noColor()
    {
        $this->option('no-color');
        return $this;
    }

    /**
     * adds `--color` option to gulp
     *
     * @return $this
     */
    public function color()
    {
        $this->option('color');
        return $this;
    }

    /**
     * adds `--tasks-simple` option to gulp
     *
     * @return $this
     */
    public function simple()
    {
        $this->option('tasks-simple');
        return $this;
    }

    public function __construct($task, $pathToGulp = null)
    {
        $this->task = $task;
        if ($pathToGulp) {
            $this->command = $pathToGulp;
        } elseif (is_executable('/usr/bin/gulp')) {
            $this->command = '/usr/bin/gulp';
        } elseif (is_executable('/usr/local/bin/gulp')) {
            $this->command = '/usr/local/bin/gulp';
        } else {
            throw new TaskException(__CLASS__, "Executable not found.");
        }
    }

    public function getCommand()
    {
        return "{$this->command} " . escapeshellarg($this->task) . "{$this->arguments}";
    }
}