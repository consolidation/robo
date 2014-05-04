<?php
namespace Robo\Task;

use Robo\Result;
use Robo\Task\Shared\CommandInterface;
use Robo\Task\Shared\TaskInterface;
use Symfony\Component\Process\Process;

trait PHPUnit {
    protected function taskPHPUnit($pathToPhpUnit = null)
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
    use \Robo\Task\Shared\Process;

    protected $command;

    public function __construct($pathToPhpUnit = null)
    {
        if ($pathToPhpUnit) {
            $this->command = $pathToPhpUnit;
        } elseif (file_exists('vendor/bin/phpunit')) {
            $this->command = 'vendor/bin/phpunit';
        } elseif (file_exists('phpunit.phar')) {
            $this->command = 'php phpunit.phar';
        } elseif (is_executable('/usr/bin/phpunit')) {
            $this->command = '/usr/bin/phpunit';
        } elseif (is_executable('/usr/local/bin/phpunit')) {
			$this->command = '/usr/local/bin/phpunit';
		} else {
            throw new Shared\TaskException(__CLASS__,"Neither local phpunit nor global composer installation not found");
        }
    }

    public function filter($filter)
    {
        $this->command .= " --filter $filter";
        return $this;
    }

    public function group($group)
    {
        $this->command .= " --group $group";
        return $this;
    }

    public function excludeGroup($group)
    {
        $this->command .= " --exclude-group $group";
        return $this;
    }

    /**
     * adds `log-json` option to runner
     *
     * @param string $file
     * @return $this
     */
    public function json($file = "")
    {
        $this->command .= " --log-json $file";
        return $this;
    }

    /**
     * adds `log-xml` option
     *
     * @param string $file
     * @return $this
     */
    public function xml($file = "")
    {
        $this->command .= " --log-xml $file";
        return $this;
    }

    public function tap($file = "")
    {
        $this->command .= " --log-tap $file";
        return $this;
    }

    public function bootstrap($file)
    {
        $this->command .= " --bootstrap $file";
        return $this;
    }

    public function configFile($file)
    {
        $this->command .= " -c $file";
        return $this;
    }

    public function debug()
    {
        $this->command .= " --debug";
        return $this;
    }

    public function option($option, $value = "")
    {
        $this->command .= " --$option $value";
        return $this;
    }

    public function arg($arg)
    {
        $this->command .= " $arg";
        return $this;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function run()
    {
        $this->printTaskInfo('Executing '. $this->getCommand());
        return $this->executeCommand($this->getCommand());
    }
}
