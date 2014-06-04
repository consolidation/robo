<?php

namespace Robo\Task;

use Robo\Output;
use Robo\Result;
use Robo\Task\Exec;
use Robo\Task\Shared\CommandInjected;
use Robo\Task\Shared\CommandInterface;
use Robo\Task\Shared\DynamicConfig;
use Robo\Task\Shared\Executable;
use Robo\Task\Shared\TaskException;
use Robo\Task\Shared\TaskInterface;

trait SshExec
{
    protected function taskSshExec($hostname = null, $user = null)
    {
        return new SshExecTask($hostname, $user);
    }
}

/**
 * Runs multiple commands on a remote server.
 * Per default, commands are combined with &&, unless stopOnFail is false.
 *
 * ``` php
 * $this->taskSshExec('remote.example.com', 'user')
 *     ->exec('cd /var/www/html')
 *     ->exec('ls -la')
 *     ->exec('chmod g+x logs')
 *     ->run();
 * ```
 *
 * You can even exec other tasks (which implement CommandInterface):
 *
 * ``` php
 * $gitTask = $this->taskGitStack()
 *     ->checkout('master')
 *     ->pull();
 *
 * $this->taskSshExec('remote.example.com')
 *     ->exec('cd /var/www/html/site')
 *     ->exec($gitTask)
 *     ->run();
 * ```
 */
class SshExecTask implements TaskInterface, CommandInterface
{
    use DynamicConfig;
    use CommandInjected;
    use Output;
    use Exec;
    use Shared\Executable;

    protected $hostname;

    protected $user;

    protected $stopOnFail = true;

    protected $exec = [];

    public function __construct($hostname = null, $user = null)
    {
        $this->hostname = $hostname;
        $this->user = $user;
    }

    public function identityFile($filename)
    {
        $this->option('-i', $filename);

        return $this;
    }

    public function port($port)
    {
        $this->option('-p', $port);

        return $this;
    }

    public function forcePseudoTty()
    {
        $this->option('-t');

        return $this;
    }

    public function quiet()
    {
        $this->option('-q');

        return $this;
    }

    public function verbose()
    {
        $this->option('-v');

        return $this;
    }

    /**
     * @param string|CommandInterface $command
     * @return $this
     */
    public function exec($command)
    {
        if (is_array($command)) {
            $command = implode(' ', array_filter($command));
        }

        $this->exec[] = $command;

        return $this;
    }

    /**
     * Returns command that can be executed.
     * This method is used to pass generated command from one task to another.
     *
     * @return string
     */
    public function getCommand()
    {
        $commands = [];
        foreach ($this->exec as $command) {
            $commands[] = $this->retrieveCommand($command);
        }
        $command = implode($this->stopOnFail ? ' && ' : ' ; ', $commands);

        return $this->sshCommand($command);
    }

    /**
     * @return \Robo\Result
     */
    public function run()
    {
        $this->validateParameters();
        $command = $this->getCommand();
        $result = $this->taskExec($command)->run();
        if (!$result->wasSuccessful()) {
            return $result;
        }

        return Result::success($this);
    }

    protected function validateParameters()
    {
        if (empty($this->hostname)) {
            throw new TaskException($this, 'Please set a hostname');
        }
        if (empty($this->exec)) {
            throw new TaskException($this, 'Please add at least one command');
        }
    }

    /**
     * Returns an ssh command string running $command on the remote.
     *
     * @param string|CommandInterface $command
     * @return string
     */
    protected function sshCommand($command)
    {
        $command = $this->retrieveCommand($command);
        $sshOptions = $this->arguments;
        $hostSpec = $this->hostname;
        if ($this->user) {
            $hostSpec = $this->user . '@' . $hostSpec;
        }

        return sprintf("ssh{$sshOptions} {$hostSpec} '{$command}'");
    }

}
