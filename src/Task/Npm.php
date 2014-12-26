<?php
namespace Robo\Task;

use Robo\Task\Shared\CommandInterface;
use Robo\Task\Shared\TaskInterface;
use Robo\Task\Shared\TaskException;

/**
 * Contains tasks for npm.
 *
 * @package  Robo\Task
 */
trait Npm {

    protected function taskNpmInstall($pathToNpm = null) {
        return new NpmInstallTask($pathToNpm);
    }

    protected function taskNpmUpdate($pathToNpm = null) {
        return new NpmUpdateTask($pathToNpm);
    }
}

abstract class BaseNpmTask {
    use \Robo\Task\Shared\Executable;
    use \Robo\Output;

    protected $opts = [];
    protected $action = '';

    /**
     * adds `production` option to npm
     *
     * @return $this
     */
    public function noDev() {
        $this->option('production');
        return $this;
    }

    public function __construct($pathToNpm = null) {
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

/**
 * Npm Install
 *
 * ``` php
 * <?php
 * // simple execution
 * $this->taskNpmInstall()->run();
 *
 * // prefer dist with custom path
 * $this->taskNpmInstall('path/to/my/npm')
 *      ->noDev()
 *      ->run();
 * ?>
 * ```
 */
class NpmInstallTask extends BaseNpmTask implements TaskInterface, CommandInterface  {

    protected $action = 'install';

    public function run() {
        $this->printTaskInfo('Install Npm packages: ' . $this->arguments);
        return $this->executeCommand($this->getCommand());
    }
}

/**
 * Npm Update
 *
 * ``` php
 * <?php
 * // simple execution
 * $this->taskNpmUpdate()->run();
 *
 * // prefer dist with custom path
 * $this->taskNpmUpdate('path/to/my/npm')
 *      ->noDev()
 *      ->run();
 * ?>
 * ```
 */
class NpmUpdateTask extends BaseNpmTask implements TaskInterface {

    protected $action = 'update';

    public function run() {
        $this->printTaskInfo('Update Npm packages: ' . $this->arguments);
        return $this->executeCommand($this->getCommand());
    }
}
