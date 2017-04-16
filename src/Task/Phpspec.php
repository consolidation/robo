<?php
namespace Robo\Task;

use Robo\Task\Shared\CommandInterface;
use Robo\Task\Shared\TaskInterface;

trait Phpspec {
    protected function taskPhpspec($pathToPhpspec = '')
    {
        return new PhpspecTask($pathToPhpspec);
    }
}

/**
 * Executes Phpspec tests
 *
 * ``` php
 * <?php
 * $this->taskPhpspec()
 *      ->run();
 * ?>
 * ```
 *
 */
class PhpspecTask implements TaskInterface, CommandInterface{
    use \Robo\Output;
    use \Robo\Task\Shared\Executable;

    protected $command;


    public function __construct($pathToPhpspec = '')
    {
        if ($pathToPhpspec) {
            $this->command = "$pathToPhpspec run";
        } elseif (file_exists('vendor/phpspec/phpspec/bin/phpspec')) {
            $this->command = 'vendor/phpspec/phpspec/bin/phpspec run';
        } elseif (file_exists('codecept.phar')) {
            $this->command = 'php phpspec.phar run';
		} else {
            throw new Shared\TaskException(__CLASS__,"Neither composer nor phar installation of Phpspec found");
        }
    }

    public function getCommand()
    {
        return $this->command . $this->arguments;
    }

    public function run()
    {
        $command = $this->getCommand();
        $this->printTaskInfo('Executing '. $command);
        return $this->executeCommand($command);
    }

} 