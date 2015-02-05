<?php
namespace Robo\Task\Base;

use Robo\Result;
use Robo\Task\BaseTask;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Executes Symfony Command
 *
 * ``` php
 * <?php
 * // Symfony Command
 * $this->taskSymfonyCommand(new \Codeception\Command\Run('run'))
 *      ->arg('suite','acceptance')
 *      ->opt('debug')
 *      ->run();
 *
 * // Artisan Command
 * $this->taskSymfonyCommand(new ModelGeneratorCommand())
 *      ->arg('name', 'User')
 *      ->run();
 * ?>
 * ```
 */
class SymfonyCommand extends BaseTask
{
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
