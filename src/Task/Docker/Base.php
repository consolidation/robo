<?php
namespace Robo\Task\Docker;

use Robo\Common\ExecOneCommand;
use Robo\Contract\PrintedInterface;
use Robo\Task\BaseTask;

abstract class Base extends BaseTask implements PrintedInterface
{
    use ExecOneCommand;

    /**
     * @var string
     */
    protected $command = '';

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $command = $this->getCommand();
        $this->printTaskInfo('Running {command}', ['command' => $command]);
        return $this->executeCommand($command);
    }

    abstract public function getCommand();
}
