<?php
namespace Robo\Task;

use Robo\Result;
use Robo\Task\Shared\CommandInterface;
use Robo\Task\Shared\TaskInterface;
use Symfony\Component\Process\Process;

trait Codeception {
    protected function taskCodecept($pathToCodeception = '')
    {
        return new CodeceptionTask($pathToCodeception);
    }
}

/**
 * Executes Codeception tests
 *
 * ``` php
 * <?php
 * $this->taskCodecept()
 *      ->suite('acceptance')
 *      ->env('chrome')
 *      ->group('admin')
 *      ->xml()
 *      ->html()
 *      ->run();
 * ?>
 * ```
 *
 */
class CodeceptionTask implements TaskInterface, CommandInterface{
    use \Robo\Output;

    public function __construct($pathToCodeception = '')
    {
        if ($pathToCodeception) {
            $this->command = $pathToCodeception;
        } elseif (file_exists('vendor/bin/codecept')) {
            $this->command = 'vendor/bin/codecept run ';
        } elseif (file_exists('codecept.phar')) {
            $this->command = 'php codecept.phar run ';
		} else {
            throw new Shared\TaskException(__CLASS__,"Neither composer nor phar installation of Codeception found");
        }
    }

    public function suite($suite)
    {
        $this->command .= " $suite";
    }

    public function option($option, $value = "")
    {
        $this->command .= " --$option $value";
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

    public function json($file = "")
    {
        $this->command .= " --json $file";
        return $this;
    }

    public function xml($file = "")
    {
        $this->command .= " --xml $file";
        return $this;
    }

    public function tap($file = "")
    {
        $this->command .= " --log-tap $file";
        return $this;
    }

    public function configFile($file)
    {
        $this->command .= " -c $file";
        return $this;
    }

    public function coverage()
    {
        $this->command .= " --coverage";
        return $this;
    }

    public function silent()
    {
        $this->command .= " --silent";
        return $this;
    }

    public function coverageXml($xml = "")
    {
        $this->command .= " --coverage-xml $xml";
        return $this;
    }

    public function coverageHtml($html = "")
    {
        $this->command .= " --coverage-html $html";
        return $this;
    }

    public function env($env)        
    {
        $this->command = " --env $env";
        return $this;
    }

    public function debug()
    {
        $this->command .= " --debug";
        return $this;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function run()
    {
        $this->printTaskInfo('Executing '. $this->command);
        $process = new Process($this->command);
        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                echo 'ER > '.$buffer;
            } else {
                echo $buffer;
            }
        });
        return new Result($this, $process->getExitCode(), $process->getOutput());
    }

} 