<?php
namespace Robo\TaskCollection;

use Robo\Task\BaseTask;
use Robo\Contract\TaskInterface;
use Robo\Contract\CompletionInterface;

/**
 * Creates a task wrapper that just calls the completion() function
 * of the provided task.
 *
 * Clients usually do not need to use this class directly; when a
 * task is added to a task collection via the add() method, the task
 * will automatically be registered with a CompletionTask if it implements
 * CompletionInterface.
 */
class CompletionTask extends BaseTask {

    private $task;

    public function __construct(CompletionInterface $task) {
        $this->task = $task;
    }

    public function run() {
        return $this->task->complete();
    }
};
