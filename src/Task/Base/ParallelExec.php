<?php
namespace Robo\Task\Base;
use Robo\Common\Timer;
use Robo\Contract\CommandInterface;
use Robo\Contract\PrintedInterface;
use Robo\Result;
use Robo\Task\BaseTask;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
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
    use Timer;
    use \Robo\Common\DynamicParams;
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

    public function getCommand()
    {
        return implode(' && ', $this->processes);
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

        $progress = new ProgressBar($this->getOutput());
        $progress->start(count($this->processes));
        $running = $this->processes;
        $progress->display();
        $this->startTimer();
        while (true) {
            foreach ($running as $k => $process) {
                try {
                    $process->checkTimeout();
                } catch (ProcessTimedOutException $e) {
                }
                if (!$process->isRunning()) {
                    $progress->advance();
                    if ($this->isPrinted) {
                        $this->getOutput()->writeln("");
                        $this->printTaskInfo("Output for <fg=white;bg=magenta> " . $process->getCommandLine()." </fg=white;bg=magenta>");
                        $this->getOutput()->writeln($process->getOutput(), OutputInterface::OUTPUT_RAW);
                        if ($process->getErrorOutput()) {
                            $this->getOutput()->writeln("<error>" . $process->getErrorOutput() . "</error>");
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
        $this->getOutput()->writeln("");
        $this->stopTimer();

        $errorMessage = '';
        $exitCode = 0;
        foreach ($this->processes as $p) {
            if ($p->getExitCode() === 0) continue;
            $errorMessage .= "'" . $p->getCommandLine() . "' exited with code ". $p->getExitCode()." \n";
            $exitCode = max($exitCode, $p->getExitCode());
        }
        if (!$errorMessage) $this->printTaskSuccess(count($this->processes) . " processes finished running");

        return new Result($this, $exitCode, $errorMessage, ['time' => $this->getExecutionTime()]);
    }
}