<?php
namespace Robo\TaskCollection;

use Robo\Task\BaseTask;
use Robo\Contract\TaskInterface;
use Robo\Contract\RollbackInterface;

/**
 * Creates a task wrapper that just calls the rollback() function
 * of the provided task.
 *
 * Clients usually do not need to use this class directly; when a
 * task is added to a task collection via the add() method, the task
 * will automatically be registered with a RollbackTask if it implements
 * RollbackInterface.
 */
class RollbackTask extends BaseTask {

    private $task;

    public function __construct(RollbackInterface $task) {
        $this->task = $task;
    }

    public function run() {
        return $this->task->rollback();
    }
};
