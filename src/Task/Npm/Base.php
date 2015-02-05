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
        if ($pathToNpm) {
            $this->command = $pathToNpm;
        } elseif (is_executable('/usr/bin/npm')) {
            $this->command = '/usr/bin/npm';
        } elseif (is_executable('/usr/local/bin/npm')) {
            $this->command = '/usr/local/bin/npm';
        } else {
            throw new TaskException(__CLASS__, "Executable not found.");
        }
    }

    public function getCommand()
    {
        return "{$this->command} {$this->action}{$this->arguments}";
    }
}
