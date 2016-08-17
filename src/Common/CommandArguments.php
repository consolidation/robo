<?php
namespace Robo\Common;

use Symfony\Component\Process\ProcessBuilder;

/**
 * Use this to add arguments and options to the $arguments property.
 */
trait CommandArguments
{
    private $processBuilder;
    private $preEscapedCommand;
    private $workingDirectory;

    public function hasProcessBuilder()
    {
        return isset($this->processBuilder);
    }

    /**
     * Provides direct access to the process builder to set up
     * args, options, redirection, etc. for the command.
     */
    protected function getProcessBuilder()
    {
        if (!$this->hasProcessBuilder()) {
            $this->processBuilder = new ProcessBuilder();
        }
        return $this->processBuilder;
    }

    /**
     * Tasks can override getCommand to alter the process builder
     * once the  command has been set up.  Alterations should be
     * idempotent.
     */
    public function getCommand()
    {
        if (!empty($this->preEscapedCommand)) {
            $result = $this->preEscapedCommand;
            if ($this->hasProcessBuilder()) {
                $result = trim($result . ' ' . $this->getProcessBuilder()->getProcess()->getCommandLine());
            }
            return $result;
        }
        if (!$this->hasProcessBuilder()) {
            throw new TaskException($this, 'Please define a command');
        }
        return $this->getProcessBuilder();
    }

    /**
     * Pass argument to executable
     *
     * @param $arg
     * @return $this
     */
    public function arg($arg)
    {
        $this->args($arg);
        return $this;
    }

    /**
     * Pass methods parameters as arguments to executable
     *
     * @param $args
     * @return $this
     */
    public function args($args)
    {
        if (!is_array($args)) {
            $args = func_get_args();
        }
        foreach ($args as $arg) {
            $this->getProcessBuilder()->add($arg);
        }
        return $this;
    }

    /**
     * Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
     *
     * Option values must be explicitly escaped with escapeshellarg if necessary before being passed to
     * this function.
     *
     * @param $option
     * @param string $value
     * $param string $sep ' ' or '='
     * @return $this
     */
    public function option($option, $value = null, $sep = ' ')
    {
        if ($option !== null and strpos($option, '-') !== 0) {
            $option = "--$option";
        }
        if (isset($value)) {
            $option .= $sep . $value;
        }
        $this->arg($option);
        return $this;
    }

    /**
     * Pass multiple options to executable. Value can be a string or array.
     *
     * Option values should be provided in raw, unescaped form. They will be
     * escaped.
     *
     * @param $option
     * @param string|array $value
     * @return $this
     */
    public function optionList($option, $value = array(), $sep = ' ')
    {
        if (is_array($value)) {
            foreach ($value as $item) {
                $this->optionList($option, $item, $sep);
            }
        } else {
            $this->option($option, $value, $sep);
        }

        return $this;
    }

    /**
     * Some tasks allow an entire pre-escaped commandline to
     * be passed in via the task constructor as an alternative
     * to using chained initializer methods.  This is generally
     * not recommended, save for simple commands that require
     * no escaping, as escaping rules are platform-specific.
     *
     * @param string $command Commandline to execute, already escaped.
     */
    public function setPreEscapedCommand($command)
    {
        $this->preEscapedCommand = $command;
    }

    public function setExecutableCommand($prefix)
    {
        $this->getProcessBuilder()->setPrefix($prefix);
        $this->preEscapedCommand = null;
        return $this;
    }

    public function dir($cwd)
    {
        $this->workingDirectory = $cwd;
        $this->getProcessBuilder()->setWorkingDirectory($cwd);
        return $this;
    }

    public function hasWorkingDirectory()
    {
        return isset($this->workingDirectory);
    }

    public function getWorkingDirectory()
    {
        return $this->workingDirectory;
    }

    public function inheritEnvironmentVariables($inheritEnv = true)
    {
        $this->getProcessBuilder()->inheritEnvironmentVariables($inheritEnv);
        return $this;
    }

    /**
     * Sets the environment variables for the command
     *
     * @param $env
     * @return $this
     */
    public function env(array $env)
    {
        $this->getProcessBuilder()->addEnvironmentVariables($env);
        return $this;
    }

    public function setEnv($name, $value)
    {
        $this->getProcessBuilder()->setEnv($name, $value);
        return $this;
    }

    public function setInput($input)
    {
        $this->getProcessBuilder()->setInput($input);
        return $this;
    }

    public function timeout($timeout)
    {
        $this->getProcessBuilder()->setTimeout($timeout);
        return $this;
    }

    public function idleTimeout($timeout)
    {
        $this->getProcessBuilder()->setIdleTimeout($timeout);
        return $this;
    }

    public function setProcOpenOption($name, $value)
    {
        $this->getProcessBuilder()->setOption($name, $value);
        return $this;
    }

    public function disableOutput()
    {
        $this->getProcessBuilder()->disableOutput();
        return $this;
    }

    public function enableOutput()
    {
        $this->getProcessBuilder()->enableOutput();
        return $this;
    }
}
