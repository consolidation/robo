<?php
namespace Robo\Task\Base;

use Robo\Contract\ProgressIndicatorAwareInterface;
use Robo\Common\ProgressIndicatorAwareTrait;
use Robo\Contract\CommandInterface;
use Robo\Contract\PrintedInterface;
use Robo\Result;
use Robo\Task\BaseTask;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

/**
 * Class ParallelExecTask
 *
 * ``` php
 * <?php
 * $this->taskParallelExec()
 *   ->process('php ~/demos/script.php hey')
 *   ->process('php ~/demos/script.php hoy')
 *   ->process('php ~/demos/script.php gou')
 *   ->run();
 * ?>
 * ```
 *
 *
 * @method \Robo\Task\Base\ParallelExec timeout(int $timeout) stops process if it runs longer then `$timeout` (seconds)
 * @method \Robo\Task\Base\ParallelExec idleTimeout(int $timeout) stops process if it does not output for time longer then `$timeout` (seconds)
 */
class ParallelExec extends BaseTask implements CommandInterface, PrintedInterface
{
    use \Robo\Common\CommandReceiver;

    protected $processes = [];
    protected $timeout = null;
    protected $idleTimeout = null;
    protected $isPrinted = false;

    public function getPrinted()
    {
        return $this->isPrinted;
    }

    public function printed($isPrinted = true)
    {
        $this->isPrinted = $isPrinted;
        return $this;
    }

    public function process($command)
    {
        $this->processes[] = new Process($this->receiveCommand($command));
        return $this;
    }

    public function timeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function idleTimeout($idleTimeout)
    {
        $this->idleTimeout = $idleTimeout;
        return $this;
    }

    public function getCommand()
    {
        return implode(' && ', $this->processes);
    }

    public function progressIndicatorSteps()
    {
        return count($this->processes);
    }

    public function run()
    {
        foreach ($this->processes as $process) {
            /** @var $process Process  **/
            $process->setIdleTimeout($this->idleTimeout);
            $process->setTimeout($this->timeout);
            $process->start();
            $this->printTaskInfo($process->getCommandLine());
        }

        $this->startProgressIndicator();
        $running = $this->processes;
        while (true) {
            foreach ($running as $k => $process) {
                try {
                    $process->checkTimeout();
                } catch (ProcessTimedOutException $e) {
                    $this->printTaskWarning("Process timed out for {command}", ['command' => $process->getCommandLine(), '_style' => ['command' => 'fg=white;bg=magenta']]);
                }
                if (!$process->isRunning()) {
                    $this->advanceProgressIndicator();
                    if ($this->isPrinted) {
                        $this->printTaskInfo("Output for {command}:\n\n{output}", ['command' => $process->getCommandLine(), 'output' => $process->getOutput(), '_style' => ['command' => 'fg=white;bg=magenta']]);
                        $errorOutput = $process->getErrorOutput();
                        if ($errorOutput) {
                            $this->printTaskError(rtrim($errorOutput));
                        }
                    }
                    unset($running[$k]);
                }
            }
            if (empty($running)) {
                break;
            }
            usleep(1000);
        }
        $this->stopProgressIndicator();

        $errorMessage = '';
        $exitCode = 0;
        foreach ($this->processes as $p) {
            if ($p->getExitCode() === 0) {
                continue;
            }
            $errorMessage .= "'" . $p->getCommandLine() . "' exited with code ". $p->getExitCode()." \n";
            $exitCode = max($exitCode, $p->getExitCode());
        }
        if (!$errorMessage) {
            $this->printTaskSuccess('{process-count} processes finished running', ['process-count' => count($this->processes)]);
        }

        return new Result($this, $exitCode, $errorMessage, ['time' => $this->getExecutionTime()]);
    }
}
