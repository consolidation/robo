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
            $this->printTaskInfo('Running Gulp task: {task} with arguments: {arguments}', ['task' => $this->task, 'arguments' => $this->arguments]);
        } else {
            $this->printTaskInfo('Running Gulp task: {task} without arguments', ['task' => $this->task]);
        }
        return $this->executeCommand($this->getCommand());
    }
}
