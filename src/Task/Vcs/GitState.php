<?php
namespace Robo\Task\Vcs;

use Robo\Result;
use Robo\Task\StackBasedTask;
use GitWrapper\GitWrapper;

/**
 * Keeps the state of the Git environment.
 */
class GitState
{
    protected $delegate;

    public function __construct($gitBinary = null)
    {
        $this->delegate = new GitWrapper($gitBinary);
    }

    public function getWrapper()
    {
        return $this->delegate;
    }

    public function getDispatcher()
    {
        return $this->delegate->getDispatcher();
    }

    public function setDispatcher($dispatcher)
    {
        $this->delegate->setDispatcher($dispatcher);
        return $this;
    }

    public function setGitBinary($gitBinary)
    {
        $this->delegate->setGitBinary($gitBinary);
        return $this;
    }

    public function getGitBinary()
    {
        return $this->delegate->getGitBinary();
    }

    public function setEnvVar($var, $value)
    {
        $this->delegate->setEnvVar($var, $value);
        return $this;
    }

    public function unsetEnvVar($var)
    {
        $this->delegate->unsetEnvVar($var);
        return $this;
    }

    public function getEnvVar($var, $default = NULL)
    {
        return $this->delegate->getEnvVar($var, $default);
    }

    public function getEnvVars()
    {
        return $this->delegate->getEnvVars();
    }

    public function setTimeout($timeout)
    {
        $this->delegate->setTimeout($timeout);
        return $this;
    }

    public function getTimeout()
    {
        return $this->delegate->getTimeout();
    }

    public function setProcOptions($options)
    {
        $this->delegate->setProcOptions($options);
        return $this;
    }

    public function getProcOptions()
    {
        return $this->delegate->getProcOptions();
    }

    public function setPrivateKey($privateKey, $port = 22, $wrapper = NULL)
    {
        $this->delegate->setPrivateKey($privateKey, $port, $wrapper);
        return $this;
    }

    public function unsetPrivateKey()
    {
        $this->delegate->unsetPrivateKey();
        return $this;
    }

    public function streamOutput($streamOutput = true)
    {
        $this->delegate->streamOutput($streamOutput);
        return $this;
    }

    public function workingCopy($directory)
    {
        return new Git('git', $this, $directory);

    }

    public function init($directory, $options = array ())
    {
        $git = $this->workingCopy($directory);
        $git->init($options);
        return $git;
    }

    public function cloneRepository($repository, $directory = NULL, $options = array ())
    {
        $git = $this->workingCopy($directory);
        $git->cloneRepository($repository, $directory, $options);
        return $git;
    }

    public function git($commandLine, $cwd = null)
    {
        return $this->delegate->git($commandLine, $cwd);
    }

    public function version()
    {
        return $this->delegate->version();
    }
}
