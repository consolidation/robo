<?php
namespace Robo\Task\Base;
use Robo\Contract\CommandInterface;
use Robo\Contract\TaskInterface;
use Robo\Result;
use Robo\Exception\TaskException;
use Symfony\Component\Console\Helper\ProgressHelper;
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
 * @method \Robo\Task\ParallelExecTask timeout(int $timeout) stops process if it runs longer then `$timeout` (seconds)
 * @method \Robo\Task\ParallelExecTask idleTimeout(int $timeout) stops process if it does not output for time longer then `$timeout` (seconds)
 */
class ParallelExec implements TaskInterface, CommandInterface
{
    use \Robo\Output;
    use \Robo\Common\DynamicConfig;
    use \Robo\Common\CommandInjected;

    protected $processes = [];
    protected $timeout = null;
    protected $idleTimeout = null;
    protected $isPrinted = false;

    public function printed($isPrinted = true)
    {
        $this->isPrinted = $isPrinted;
        return $this;
    }

    public function process($command)
    {
        $this->processes[] = new Process($this->retrieveCommand($command));
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

        $progress = new ProgressHelper();
        $progress->setFormat(" <fg=white;bg=cyan;options=bold>[".get_class($this)."]</fg=white;bg=cyan;options=bold> Processes: %current%/%max% [%bar%] %percent%%");
        $progress->start($this->getOutput(), count($this->processes));
        $running = $this->processes;
        $progress->display();
        $started = microtime(true);
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
        $taken = number_format(microtime(true) - $started, 2);
        $this->printTaskInfo(count($this->processes) . " processes ended in $taken s");

        $errorMessage = '';
        $exitCode = 0;
        foreach ($this->processes as $p) {
            if ($p->getExitCode() === 0) continue;
            $errorMessage .= "'" . $p->getCommandLine() . "' exited with code ". $p->getExitCode()." \n";
            $exitCode = max($exitCode, $p->getExitCode());
        }

        return new Result($this, $exitCode, $errorMessage);
    }
} 