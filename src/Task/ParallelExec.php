<?php
namespace Robo\Task;
use Robo\Result;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

trait ParallelExec {
    protected function parallelExec()
    {
        return new ParallelExecTask();
    }
}

/**
 * Class ParallelExecTask
 * @package Robo\Task
 *
 * @method \Robo\Task\ParallelExecTask timeout(int $timeout)
 * @method \Robo\Task\ParallelExecTask idleTimeout(int $timeout)
 */
class ParallelExecTask implements Shared\TaskInterface
{
    use \Robo\Output;
    use Shared\DynamicConfig;

    protected $processes = [];
    protected $timeout = 3600;
    protected $idleTimeout = 60;

    public function process($command)
    {
        $this->processes[] = new Process($command);
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
        $progress->start($this->getOutput(), count($this->processes));
        $running = $this->processes;
        $progress->display();
        while (true) {
            foreach ($running as $k => $process) {
                try {
                    $process->checkTimeout();
                } catch (ProcessTimedOutException $e) {
                }
                if (!$process->isRunning()) {
                    $progress->advance();
                    unset($running[$k]);
                }
            }
            if (empty($running)) {
                break;
            }
            usleep(1000);
        }
        $this->getOutput()->writeln("");
        $this->printTaskInfo(count($this->processes) . " processes ended");

        $exitCode = max(array_map(function(Process $p) { return $p->getExitCode(); }, $this->processes));
        return new Result($this, $exitCode);
    }
} 