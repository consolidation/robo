<?php

namespace Robo\Task\Remote;

use Robo\Result;
use Robo\Contract\CommandInterface;
use Robo\Exception\TaskException;
use Robo\Task\BaseTask;

/**
 * Runs multiple commands on a remote server.
 * Per default, commands are combined with &&, unless stopOnFail is false.
 *
 * ```php
 * <?php
 *
 * $this->taskSshExec('remote.example.com', 'user')
 *     ->remoteDir('/var/www/html')
 *     ->exec('ls -la')
 *     ->exec('chmod g+x logs')
 *     ->run();
 *
 * ```
 *
 * You can even exec other tasks (which implement CommandInterface):
 *
 * ```php
 * $gitTask = $this->taskGitStack()
 *     ->checkout('master')
 *     ->pull();
 *
 * $this->taskSshExec('remote.example.com')
 *     ->remoteDir('/var/www/html/site')
 *     ->exec($gitTask)
 *     ->run();
 * ```
 *
 * You can configure the remote directory for all future calls:
 *
 * ```php
 * \Robo\Task\Remote\Ssh::configure('remoteDir', '/some-dir');
 * ```
 *
 * @method $this stopOnFail(bool $stopOnFail) Whether or not to chain commands together with &&
 *                                            and stop the chain if one command fails
 * @method $this remoteDir(string $remoteWorkingDirectory) Changes to the given directory before running commands
 */
class Ssh extends BaseTask implements CommandInterface
{
    use \Robo\Common\CommandReceiver;
    use \Robo\Common\ExecOneCommand;

    protected $builtCommand;

    protected $hostname;

    protected $user;

    protected $stopOnFail = true;

    protected $exec = [];

    /**
     * Changes to the given directory before running commands.
     *
     * @var string
     */
    protected $remoteDir;

    public function __construct($hostname = null, $user = null)
    {
        $this->hostname = $hostname;
        $this->user = $user;
        $this->setExecutableCommand('ssh');
    }

    public function hostname($hostname)
    {
        $this->hostname = $hostname;
        return $this;
    }

    public function user($user)
    {
        $this->user = $user;
        return $this;
    }

    public function stopOnFail($stopOnFail = true)
    {
        $this->stopOnFail = $stopOnFail;
        return $this;
    }

    public function remoteDir($remoteDir)
    {
        $this->remoteDir = $remoteDir;
        return $this;
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
        $this->buildSshCommand();
        return parent::getCommand();
    }

    protected function buildSshCommand()
    {
        if ($this->builtCommand) {
            return;
        }
        $this->buildHouseSpecs();
        $this->buildRemoteCommand();
        $this->builtCommand = true;
    }

    protected function buildRemoteCommand()
    {
        $commands = [];
        foreach ($this->exec as $command) {
            $process = $this->receiveCommand($command);
            $commands[] = $process->getCommandLine();
        }

        $remoteDir = $this->remoteDir ? $this->remoteDir : $this->getConfigValue('remoteDir');
        if (!empty($remoteDir)) {
            array_unshift($commands, sprintf('cd "%s"', $remoteDir));
        }
        $command = implode($this->stopOnFail ? ' && ' : ' ; ', $commands);

        $this->arg($command);
    }

    /**
     * @return \Robo\Result
     */
    public function run()
    {
        $this->validateParameters();
        $command = $this->getCommand();
        return $this->executeCommand($command);
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
     * @param string $command
     * @return string
     */
    protected function buildHouseSpecs()
    {
        $hostSpec = $this->hostname;
        if ($this->user) {
            $hostSpec = $this->user . '@' . $hostSpec;
        }

        $this->arg($hostSpec);
    }
}
