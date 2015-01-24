<?php
namespace Robo\Task\Base;

use Robo\Contract\CommandInterface;
use Robo\Contract\PrintedInterface;
use Robo\Task\BaseTask;
use Symfony\Component\Process\Process;
use Robo\Result;

/**
 * Executes shell script. Closes it when running in background mode.
 *
 * ``` php
 * <?php
 * $this->taskExec('compass')->arg('watch')->run();
 * // or use shortcut
 * $this->_exec('compass watch');
 *
 * $this->taskExec('compass watch')->background()->run();
 *
 * if ($this->taskExec('phpunit .')->run()->wasSuccessful()) {
 *  $this->say('tests passed');
 * }
 *
 * ?>
 * ```
 */
class Exec extends BaseTask implements CommandInterface, PrintedInterface
{
    use \Robo\Common\CommandReceiver;
    use \Robo\Common\ExecOneCommand;

    static $instances = [];

    protected $command;
    protected $background = false;
    protected $timeout = null;
    protected $idleTimeout = null;

    /**
     * @var Process
     */
    protected $process;

    public function __construct($command)
    {
        $this->command = $this->receiveCommand($command);
    }

    public function getCommand()
    {
        return trim($this->command . $this->arguments);
    }

    /**
     * Executes command in background mode (asynchronously)
     *
     * @return $this
     */
    public function background()
    {
        self::$instances[] = $this;
        $this->background = true;
        return $this;
    }

    /**
     * Stop command if it runs longer then $timeout in seconds
     *
     * @param $timeout
     * @return $this
     */
    public function timeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Stops command if it does not output something for a while
     *
     * @param $timeout
     * @return $this
     */
    public function idleTimeout($timeout)
    {
        $this->idleTimeout = $timeout;
        return $this;
    }

    public function __destruct()
    {
        $this->stop();
    }

    protected function stop()
    {
        if ($this->background && $this->process->isRunning()) {
            $this->process->stop();
            $this->printTaskInfo("stopped <info>".$this->getCommand()."</info>");
        }
    }

    public function run()
    {
        $command = $this->getCommand();
        $dir = $this->workingDirectory ? " in " . $this->workingDirectory : "";
        $this->printTaskInfo("running <info>{$command}</info>$dir");
        $this->process = new Process($command);
        $this->process->setTimeout($this->timeout);
        $this->process->setIdleTimeout($this->idleTimeout);
        $this->process->setWorkingDirectory($this->workingDirectory);

        if (!$this->background and !$this->isPrinted) {
            $this->startTimer();
            $this->process->run();
            $this->stopTimer();
            return new Result($this, $this->process->getExitCode(), $this->process->getOutput(), ['time' => $this->getExecutionTime()]);
        }

        if (!$this->background and $this->isPrinted) {
            $this->startTimer();
            $this->process->run(
                function ($type, $buffer) {
                    print($buffer);
                }
            );
            $this->stopTimer();
            return new Result($this, $this->process->getExitCode(), $this->process->getOutput(), ['time' => $this->getExecutionTime()]);
        }

        try {
            $this->process->start();
        } catch (\Exception $e) {
            return Result::error($this, $e->getMessage());
        }
        return Result::success($this);
    }

    static function stopRunningJobs()
    {
        foreach (self::$instances as $instance) {
            if ($instance) unset($instance);
        }
    }
}

if (function_exists('pcntl_signal')) {
    pcntl_signal(SIGTERM, ['Robo\Task\Base\Exec', 'stopRunningJobs']);
}

register_shutdown_function(['Robo\Task\Base\Exec', 'stopRunningJobs']);