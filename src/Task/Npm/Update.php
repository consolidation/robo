<?php
namespace Robo\Task\Npm;

use Robo\Task\Npm;
use Robo\Task\Shared\TaskInterface;

/**
 * Npm Update
 *
 * ```php
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
class Update extends Base implements TaskInterface
{
    protected $action = 'update';

    public function run()
    {
        $this->printTaskInfo('Update Npm packages: ' . $this->arguments);
        return $this->executeCommand($this->getCommand());
    }
}