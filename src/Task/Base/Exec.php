<?php
namespace Robo\Task\Base;

use Robo\Contract\CommandInterface;
use Robo\Contract\PrintedInterface;
use Robo\Contract\SimulatedInterface;
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
class Exec extends BaseTask implements CommandInterface, PrintedInterface, SimulatedInterface
{
    use \Robo\Common\CommandReceiver;
    use \Robo\Common\ExecOneCommand;

    protected static $instances = [];

    protected $background = false;
    protected $env = null;

    /**
     * @var Process
     */
    protected $process;

    public function __construct($command)
    {
        if ($command) {
            $this->setPreEscapedCommand($this->receiveCommand($command)->getCommandLine());
        }
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

    public function __destruct()
    {
        $this->stop();
    }

    protected function stop()
    {
        if ($this->background && $this->process->isRunning()) {
            $this->process->stop();
            $this->printTaskInfo("Stopped {command}", ['command' => $this->getCommand()]);
        }
    }

    protected function printAction($context = [])
    {
        $command = $this->getCommand();
        $dir = $this->hasWorkingDirectory() ? " in {dir}" : "";
        $this->printTaskInfo("Running {command}$dir", ['command' => $command, 'dir' => $this->getWorkingDirectory()] + $context);
    }

    public function run()
    {
        $this->printAction();
        $this->process = $this->receiveCommand($this->getCommand());

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
                    $progressWasVisible = $this->hideTaskProgress();
                    print($buffer);
                    $this->showTaskProgress($progressWasVisible);
                }
            );
            $this->stopTimer();
            return new Result($this, $this->process->getExitCode(), $this->process->getOutput(), ['time' => $this->getExecutionTime()]);
        }

        try {
            $this->process->start();
        } catch (\Exception $e) {
            return Result::fromException($this, $e);
        }
        return Result::success($this);
    }

    public function simulate($context)
    {
        $this->printAction($context);
    }

    public static function stopRunningJobs()
    {
        foreach (self::$instances as $instance) {
            if ($instance) {
                unset($instance);
            }
        }
    }
}

if (function_exists('pcntl_signal')) {
    pcntl_signal(SIGTERM, ['Robo\Task\Base\Exec', 'stopRunningJobs']);
}

register_shutdown_function(['Robo\Task\Base\Exec', 'stopRunningJobs']);
