<?php
namespace Robo;

use Robo\Common\TaskIO;
use Robo\Contract\PrintedInterface;
use Robo\Contract\TaskInterface;

class Result
{
    use TaskIO;

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
        
        $this->printResult();
        
        if (self::$stopOnFail) {
            $this->stopOnFail();
        }
    }
    
    protected function printResult()
    {
        if (!$this->wasSuccessful()) {
            $this->printError($this->task);
        } else {
            $this->printSuccess($this->task);
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

    public function getExecutionTime()
    {
        if (!is_array($this->data)) return null;
        if (!isset($this->data['time'])) return null;
        $rawTime = $this->data['time'];
        return round($rawTime, 3).'s';
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
            $this->printTaskError("Stopping on fail. Exiting....");
            $this->printTaskError("<error>Exit Code: {$this->exitCode}</error>");
            exit($this->exitCode);
        }
        return $this;
    }

    protected function printError()
    {
        $lines = explode("\n", $this->message);

        $printOutput = true;

        $time = $this->getExecutionTime();
        if ($time) $time = "Time <fg=yellow>$time</fg=yellow>";

        if ($this->task instanceof PrintedInterface) {
            $printOutput = !$this->task->getPrinted();
        }
        if ($printOutput) {
            foreach ($lines as $msg) {
                if (!$msg) continue;
                $this->printTaskError($msg, $this->task);
            }
        }
        $this->printTaskError("<error> Exit code " . $this->exitCode. " </error> $time", $this->task);
    }

    protected function printSuccess()
    {
        $time = $this->getExecutionTime();
        if (!$time) return;
        $time = "in <fg=yellow>$time</fg=yellow>";
        $this->printTaskSuccess("Done $time", $this->task);
    }

} 
