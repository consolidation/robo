<?php
namespace Robo\Task;

use Robo\Add\Output;
use Robo\TaskInterface;
use \Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class Command implements TaskInterface {
    use Output;

    /**
     * @var SymfonyCommand
     */
    protected $command;

    /**
     * @var InputInterface
     */
    protected $input;

    public function __construct(SymfonyCommand $command)
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
        return $this->command->run(new ArrayInput($this->input), $this->getOutput());
    }

}
 