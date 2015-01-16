<?php
namespace Robo\Task\Npm;

use Robo\Contract\CommandInterface;

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
class Install extends Base implements CommandInterface
{
    protected $action = 'install';

    public function run()
    {
        $this->printTaskInfo('Install Npm packages: ' . $this->arguments);
        return $this->executeCommand($this->getCommand());
    }
}