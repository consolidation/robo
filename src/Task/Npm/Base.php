<?php
namespace Robo\Task\Npm;

use Robo\Task\BaseTask;
use Robo\Exception\TaskException;

abstract class Base extends BaseTask
{
    use \Robo\Common\ExecOneCommand;

    protected $opts = [];
    protected $action = '';

    /**
     * adds `production` option to npm
     *
     * @return $this
     */
    public function noDev()
    {
        $this->option('production');
        return $this;
    }

    public function __construct($pathToNpm = null)
    {
        $this->command = $pathToNpm;
        if (!$this->command) {
            $this->command = $this->findExecutable('npm');
        }
        if (!$this->command) {
            throw new TaskException(__CLASS__, "Npm executable not found.");
        }
    }

    public function getCommand()
    {
        return "{$this->command} {$this->action}{$this->arguments}";
    }
}
