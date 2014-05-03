<?php
namespace Robo\Task;
use Robo\Result;
use Robo\Task\Shared\TaskException;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

trait ParallelExec {
    protected function taskParallelExec()
    {
        return new ParallelExecTask();
    }
}

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
 * @method \Robo\Task\ParallelExecTask timeout(int $timeout)
 * @method \Robo\Task\ParallelExecTask idleTimeout(int $timeout)
 */
class ParallelExecTask implements Shared\TaskInterface
{
    use \Robo\Output;
    use Shared\DynamicConfig;
    use Shared\CommandInjected;

    protected $processes = [];
    protected $timeout = 3600;
    protected $idleTimeout = 60;
    protected $isPrinted = false;

    public function process($command)
    {
        $this->processes[] = new Process($this->retrieveCommand($command));
        return $this;
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
                        $this->getOutput()->writeln($process->getOutput(), \Symfony\Component\Console\Output\OutputInterface::OUTPUT_RAW);
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

        $exitCode = max(array_map(function(Process $p) { return $p->getExitCode(); }, $this->processes));
        return new Result($this, $exitCode);
    }
} 