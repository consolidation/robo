<?php

namespace Robo\Task\Base;

use Robo\Common\ExecTrait;
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
     * @param string|\Robo\Contract\CommandInterface $command
     */
    public function __construct($command)
    {
        $this->command = $this->receiveCommand($command);
    }

    public function __destruct()
    {
        $this->stop();
    }

    /**
     * Executes command in background mode (asynchronously)
     *
     * @param bool $arg
     *
     * @return $this
     */
    public function background($arg = true)
    {
        self::$instances[] = $this;
        $this->background = $arg;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getCommandDescription()
    {
        return $this->getCommand();
    }
    /**
     * {@inheritdoc}
     */
    public function getCommand()
    {
        return trim($this->command . $this->arguments);
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

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->hideProgressIndicator();
        // TODO: Symfony 4 requires that we supply the working directory.
        $result_data = $this->execute(new Process($this->getCommand(), getcwd()));
        return new Result(
            $this,
            $result_data->getExitCode(),
            $result_data->getMessage(),
            $result_data->getData()
        );
        $this->showProgressIndicator();
    }
}

if (function_exists('pcntl_signal')) {
    pcntl_signal(SIGTERM, ['Robo\Task\Base\Exec', 'stopRunningJobs']);
}

register_shutdown_function(['Robo\Task\Base\Exec', 'stopRunningJobs']);
