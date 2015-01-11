<?php
namespace Robo\Task\Npm;

use Robo\Task\Npm;
use Robo\Task\Shared\TaskInterface;
use Robo\Task\Shared\CommandInterface;

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
class Install extends Base implements TaskInterface, CommandInterface
{
    protected $action = 'install';

    public function run()
    {
        $this->printTaskInfo('Install Npm packages: ' . $this->arguments);
        return $this->executeCommand($this->getCommand());
    }
}