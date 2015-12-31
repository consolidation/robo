<?php
namespace Robo\TaskCollection;

use Robo\Contract\TaskInterface;

/**
 * Creates a task wrapper that will add the provided rollback task
 * to the provided collection's rollback stack if the run() method
 * of the provided primary class is called.  Thus, the
 * rollback task will only run if its associated task runs, AND there
 * is a failure in that task, or some later task.
 *
 * Clients usually do not need to use this class directly; when a
 * task is added to a task collection via the add() or addWithRollback()
 * methods, it will automatically be protected with RollbackController.
 */
class RollbackController implements TaskInterface {

    private $collection;
    private $task;
    private $rollbackTask;

    public function __construct(Collection $collection, TaskInterface $task, TaskInterface $rollbackTask) {
        $this->collection = $collection;
        $this->task = $task;
        $this->rollbackTask = $rollbackTask;
    }

    public function run() {
        $this->collection->registerRollback($this->rollbackTask);
        return $this->task->run();
    }
};
