<?php
namespace Robo\Task\Gulp;

use Robo\Task\Gulp;
use Robo\Contract\CommandInterface;

/**
 * Gulp Run
 *
 * ``` php
 * <?php
 * // simple execution
 * $this->taskGulpRun()->run();
 *
 * // run task 'clean' with --silent option
 * $this->taskGulpRun('clean')
 *      ->silent()
 *      ->run();
 * ?>
 * ```
 */
class Run extends Base implements CommandInterface
{
    public function run()
    {
        if (strlen($this->arguments)) {
            $this->printTaskInfo('Running Gulp task: ' . $this->task . ' with arguments: ' . $this->arguments);
        } else {
            $this->printTaskInfo('Running Gulp task: ' . $this->task . ' without arguments');
        }
        return $this->executeCommand($this->getCommand());
    }
}