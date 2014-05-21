<?php
namespace Robo\Task;

use Robo\Task\Shared\CommandInterface;
use Robo\Task\Shared\TaskInterface;
use Robo\Task\Shared\TaskException;

/**
 * Contains tasks for grunt.
 *
 * @package Robo\Task
 */
trait Grunt
{
    protected function taskGrunt($pathToGrunt = null) {
        return new GruntTask($pathToGrunt);
    }
}

/**
 * Grunt build
 *
 * ``` php
 * <?php
 * // simple execution
 * $this->taskGrunt()->run();
 * 
 * // specify task to execute
 * $this->taskGrunt()->arg('build')->run();
 *
 * // prefer dist with custom path
 * $this->taskGrunt('path/to/my/grunt')
 *      ->noDev()
 *      ->run();
 * ?>
 * ```
 */
class GruntTask implements TaskInterface, CommandInterface
{    
    use \Robo\Task\Shared\Executable;
    use \Robo\Output;

    public function __construct($pathToGrunt = null)
    {
        if ($pathToGrunt) {
            $this->command = $pathToGrunt;
        } elseif (is_executable('/usr/bin/grunt')) {
            $this->command = '/usr/bin/grunt';
        } elseif (is_executable('/usr/local/bin/grunt')) {
            $this->command = '/usr/local/bin/grunt';
        } else {
            throw new TaskException(__CLASS__, "Executable not found.");
        }
    }

    public function getCommand()
    {
        return "{$this->command}{$this->arguments}";
    }

    public function run()
    {
        $this->printTaskInfo('Run grunt: ' . $this->arguments);
        return $this->executeCommand($this->getCommand());
    }
}
