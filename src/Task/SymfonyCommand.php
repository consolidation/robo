<?php
namespace Robo\Task;

use Robo\Result;
use \Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

trait SymfonyCommand {

    protected function taskSymfonyCommand($command)
    {
        return new SymfonyCommandTask($command);
    }
}

class SymfonyCommandTask implements TaskInterface {
    use \Robo\Output;

    /**
     * @var SymfonyCommand
     */
    protected $command;

    /**
     * @var InputInterface
     */
    protected $input;

    public function __construct(Command $command)
    {
        $this->command = $command;
        $this->input = [];
    }

    public function arg($arg, $value)
    {
        $this->input[$arg] = $value;
        return $this;
    }

    public function opt($option, $value = null)
    {
        $this->input["--$option"] = $value;
        return $this;
    }

    public function run()
    {
        $this->printTaskInfo("Running command ".$this->command->getName());
        return new Result($this,
            $this->command->run(new ArrayInput($this->input), $this->getOutput())
        );
    }
}
