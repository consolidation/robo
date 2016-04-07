<?php
namespace Robo;

use Robo\Config;
use Robo\TaskInfo;
use Robo\Contract\TaskInterface;
use Robo\Contract\LogResultInterface;
use Consolidation\AnnotationCommand\ExitCodeInterface;
use Consolidation\AnnotationCommand\OutputDataInterface;

class Result extends ResultData
{
    public static $stopOnFail = false;
    protected $task;

    public function __construct(TaskInterface $task, $exitCode, $message = '', $data = [])
    {
        parent::__construct($exitCode, $message, $data);
        $this->task = $task;

        // For historic reasons, the Result constructor is responsible
        // for printing task results.
        // TODO: Make IO the responsibility of some other class. Maintaining
        // existing behavior for backwards compatibility. This is undesirable
        // in the long run, though, as it can result in unwanted repeated input
        // in task collections et. al.
        $resultPrinter = Config::resultPrinter();
        if ($resultPrinter) {
            $resultPrinter->printResult($this);
        }

        if (self::$stopOnFail) {
            $this->stopOnFail();
        }
    }

    public static function errorMissingExtension(TaskInterface $task, $extension, $service)
    {
        $messageTpl = 'PHP extension required for %s. Please enable %s';
        $message = sprintf($messageTpl, $service, $extension);

        return self::error($task, $message);
    }

    public static function errorMissingPackage(TaskInterface $task, $class, $package)
    {
        $messageTpl = 'Class %s not found. Please install %s Composer package';
        $message = sprintf($messageTpl, $class, $package);

        return self::error($task, $message);
    }

    public static function error(TaskInterface $task, $message, $data = [])
    {
        return new self($task, 1, $message, $data);
    }

    public static function success(TaskInterface $task, $message = '', $data = [])
    {
        return new self($task, 0, $message, $data);
    }

    public static function cancelled($message = '', $data = [])
    {
        return new ResultData(self::EXITCODE_USER_ABORT, $message, $data);
    }

    /**
     * Return a context useful for logging messages.
     */
    public function getContext()
    {
        $task = $this->getTask();

        return TaskInfo::getTaskContext($task) + [
            'code' => $this->getExitCode(),
            'data' => $this->getData(),
            'time' => $this->getExecutionTime(),
            'message' => $this->getMessage(),
        ];
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

    /**
     * @deprecated since 1.0.  @see wasSuccessful()
     */
    public function __invoke()
    {
        trigger_error(__METHOD__ . ' is deprecated: use wasSuccessful() instead.', E_USER_DEPRECATED);
        return $this->wasSuccessful();
    }

    public function stopOnFail()
    {
        if (!$this->wasSuccessful()) {
            $resultPrinter = Config::resultPrinter();
            if ($resultPrinter) {
                $resultPrinter->printStopOnFail($this);
            }
            exit($this->exitCode);
        }
        return $this;
    }
}
