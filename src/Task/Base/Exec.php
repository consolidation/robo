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

    /**
     * @var static[]
     */
    protected static $instances = [];

    /**
     * @var string|\Robo\Contract\CommandInterface
     */
    protected $command;

    /**
     * @var bool
     */
    protected $background = false;

    /**
     * @var null|int
     */
    protected $timeout = null;

    /**
     * @var null|int
     */
    protected $idleTimeout = null;

    /**
     * @var null|array
     */
    protected $env = null;

    /**
     * @var Process
     */
    protected $process;

    /**
     * @param string|\Robo\Contract\CommandInterface $command
     */
    public function __construct($command)
    {
        $this->command = $this->receiveCommand($command);
    }

    /**
     * {@inheritdoc}
     */
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
     * @param int $timeout
     *
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
     * @param int $timeout
     *
     * @return $this
     */
    public function idleTimeout($timeout)
    {
        $this->idleTimeout = $timeout;
        return $this;
    }

    /**
     * Sets the environment variables for the command
     *
     * @param array $env
     *
     * @return $this
     */
    public function env(array $env)
    {
        $this->env = $env;
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

    /**
     * @param array $context
     */
    protected function printAction($context = [])
    {
        $command = $this->getCommand();
        $dir = $this->workingDirectory ? " in {dir}" : "";
        $this->printTaskInfo("Running {command}$dir", ['command' => $command, 'dir' => $this->workingDirectory] + $context);
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->printAction();
        $this->process = new Process($this->getCommand());
        $this->process->setTimeout($this->timeout);
        $this->process->setIdleTimeout($this->idleTimeout);
        $this->process->setWorkingDirectory($this->workingDirectory);

        if (isset($this->env)) {
            $this->process->setEnv($this->env);
        }

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

    /**
     * {@inheritdoc}
     */
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
