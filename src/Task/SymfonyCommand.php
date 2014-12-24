<?php
namespace Robo\Task;

use Robo\Result;
use Robo\Task\Shared\TaskInterface;
use \Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Launch Symfony or Artisan Command
 */
trait SymfonyCommand {

    protected function taskSymfonyCommand($command)
    {
        return new SymfonyCommandTask($command);
    }
}

/**
 * Executes Symfony Command
 *
 * ``` php
 * <?php
 * // Symfony Command
 * $this->taskCommand(new \Codeception\Command\Run('run'))
 *      ->arg('suite','acceptance')
 *      ->opt('debug')
 *      ->run();
 *
 * // Artisan Command
 * $this->taskCommand(new ModelGeneratorCommand())
 *      ->arg('name', 'User')
 *      ->run();
 * ?>
 * ```
 */
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
