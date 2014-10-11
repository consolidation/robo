<?php
namespace Robo\Task;

use Robo\Result;
use Robo\Task\Shared\CommandInterface;
use Robo\Task\Shared\TaskInterface;
use Symfony\Component\Process\Process;

trait PHPUnit {
    protected function taskPHPUnit($pathToPhpUnit = 'phpunit')
    {
        return new PHPUnitTask($pathToPhpUnit);
    }
}

/**
 * Runs PHPUnit tests
 *
 * ``` php
 * <?php
 * $this->taskPHPUnit()
 *  ->group('core')
 *  ->bootstrap('test/bootstrap.php')
 *  ->run()
 *
 * ?>
 * ```
 */
class PHPUnitTask implements TaskInterface, CommandInterface
{
    use \Robo\Output;
    use \Robo\Task\Shared\Executable;

    protected $command;

    public function __construct($pathToPhpUnit = 'phpunit')
    {
        $this->command = $pathToPhpUnit;
    }

    public function filter($filter)
    {        
        $this->option('filter', $filter);
        return $this;
    }

    public function group($group)
    {
        $this->option("group", $group);
        return $this;
    }

    public function excludeGroup($group)
    {
        $this->option("exclude-group", $group);
        return $this;
    }

    /**
     * adds `log-json` option to runner
     *
     * @param string $file
     * @return $this
     */
    public function json($file = null)
    {
        $this->option("log-json", $file);
        return $this;
    }

    /**
     * adds `log-xml` option
     *
     * @param string $file
     * @return $this
     */
    public function xml($file = null)
    {
        $this->option("log-xml", $file);
        return $this;
    }

    public function tap($file = "")
    {
        $this->option("log-tap", $file);
        return $this;
    }

    public function bootstrap($file)
    {
        $this->option("bootstrap", $file);
        return $this;
    }

    public function configFile($file)
    {
        $this->option('-c', $file);
        return $this;
    }

    public function debug()
    {
        $this->option("debug");
        return $this;
    }

    public function getCommand()
    {
        return $this->command . $this->arguments;
    }

    public function run()
    {
        $this->printTaskInfo('Running PHPUnit '. $this->arguments);
        return $this->executeCommand($this->getCommand());
    }
}
