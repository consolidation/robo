<?php
namespace Robo\TaskCollection;

use Robo\Contract\TaskInterface;

/**
 * Creates a task wrapper that will protect the caller from any
 * error contditions that might be raised by the provided task.
 *
 * Clients usually do not need to use this class directly; when a
 * task is added to a task collection via the addAndIgnoreErrors()
 * method, it will automatically be protected with IgnoreErrorsTaskWrapper.
 */
class IgnoreErrorsTaskWrapper implements TaskInterface {

    private $task;

    public function __construct(TaskInterface $task) {
        $this->task = $task;
    }

    /**
     * Run the task; catch any errors, and always return a 'success'
     */
    public function run() {
        $data = [];
        try {
            $result = $this->task->run();
            $message = $result->getMessage();
            $data = $result->getData();
            $data['exitcode'] = $result->getExitCode();
        }
        catch (Exception $e) {
            $message = $e->getMessage();
        }
        return Result::success($this->task, $message, $data);
    }
};
