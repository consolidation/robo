<?php 
namespace Robo\Task\Docker;

use Robo\Common\CommandReceiver;

/**
 * Executes command inside running Docker container
 *
 * ```php
 * <?php
 * $test = $this->taskDockerRun('test_env')
 *      ->detached()
 *      ->run();
 *
 * $this->taskDockerExec($test)
 *      ->interactive()
 *      ->exec('./runtests')
 *      ->run();
 *
 * // alternatively use commands from other tasks
 *
 * $this->taskDockerExec($test)
 *      ->interactive()
 *      ->exec($this->taskCodecept()->suite('acceptance'))
 *      ->run();
 * ?>
 * ```
 *
 */
class Exec extends Base
{
    use CommandReceiver;

    protected $command = "docker exec";
    protected $cid;
    protected $run = '';


    public function __construct($cidOrResult)
    {
        $this->cid = $cidOrResult instanceof Result ? $cidOrResult->getCid() : $cidOrResult;
    }

    public function detached()
    {
        $this->option('-d');
        return $this;
    }

    public function interactive()
    {
        $this->option('-i');
        return $this;
    }

    public function exec($command)
    {
        $this->run = $this->receiveCommand($command);
        return $this;
    }

    public function getCommand()
    {
        return $this->command . ' ' . $this->arguments . ' ' . $this->cid.' '.$this->run;
    }

}