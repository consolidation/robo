<?php
namespace Robo;

use Robo\Task\Shared\TaskInterface;

class Result {
    use Output;

    static $stopOnFail = false;

    protected $exitCode;
    protected $message;
    protected $data = [];
    protected $task;
    protected $previousTask;

    public function __construct(TaskInterface $task, $exitCode, $message = '', $data = [])
    {
        $this->task = $task;
        $this->exitCode = $exitCode;
        $this->message = $message;
        $this->data = $data;
        
        if (!$this->wasSuccessful()) {
            $lines = explode("\n", $this->message);
            foreach ($lines as $msg) {
                if ($msg) $this->printTaskInfo("<error>Error</error> $msg", $this->task);
            }
            if (!$this->message) {
                $this->printTaskInfo("<error> Error</error> Exit code ".$this->exitCode, $this->task);
            }
        }

        if (self::$stopOnFail) {
            $this->stopOnFail();
        }
    }

    static function error(TaskInterface $task, $message, $data = [])
    {
        return new self($task, 1, $message, $data);
    }

    static function success(TaskInterface $task, $message = '', $data = [])
    {
        return new self($task, 0, $message, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return TaskInterface
     */
    public function getTask()
    {
        return $this->task;
    }

    public function cloneTask()
    {
        $reflect  = new \ReflectionClass(get_class($this->task));
        return $reflect->newInstanceArgs(func_get_args());
    }

    public function wasSuccessful()
    {
        return $this->exitCode === 0;
    }

    public function __invoke()
    {
        return $this->wasSuccessful();
    }

    public function stopOnFail()
    {
        if (!$this->wasSuccessful()) {
            $this->say("<error>Error running task ".get_class($this->task).". Exiting...</error>");
            $this->say("<error>Exit Code: {$this->exitCode}</error>");
            exit($this->exitCode);
        }
        return $this;
    }

} 
