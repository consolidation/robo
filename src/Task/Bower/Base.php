<?php
namespace Robo\Task\Bower;

use Robo\Task\BaseTask;
use Robo\Exception\TaskException;

abstract class Base extends BaseTask
{
    use \Robo\Common\ExecOneCommand;

    protected $opts = [];
    protected $action = '';


    /**
     * adds `allow-root` option to bower
     *
     * @return $this
     */
    public function allowRoot()
    {
        $this->option('allow-root');
        return $this;
    }

    /**
     * adds `force-latest` option to bower
     *
     * @return $this
     */
    public function forceLatest()
    {
        $this->option('force-latest');
        return $this;
    }

    /**
     * adds `production` option to bower
     *
     * @return $this
     */
    public function noDev()
    {
        $this->option('production');
        return $this;
    }

    /**
     * adds `offline` option to bower
     *
     * @return $this
     */
    public function offline()
    {
        $this->option('offline');
        return $this;
    }

    public function __construct($pathToBower = null)
    {
        if ($pathToBower) {
            $this->command = $pathToBower;
        } elseif (is_executable('/usr/bin/bower')) {
            $this->command = '/usr/bin/bower';
        } elseif (is_executable('/usr/local/bin/bower')) {
            $this->command = '/usr/local/bin/bower';
        } else {
            throw new TaskException(__CLASS__, "Executable not found.");
        }
    }

    public function getCommand()
    {
        return "{$this->command} {$this->action}{$this->arguments}";
    }
}