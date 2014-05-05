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
    use \Robo\Task\Shared\Process;

    protected $suite = '';
    protected $test = '';
    protected $options = '';


    public function __construct($pathToCodeception = '')
    {
        if ($pathToCodeception) {
            $this->options = "$pathToCodeception run";
        } elseif (file_exists('vendor/bin/codecept')) {
            $this->options = 'vendor/bin/codecept run';
        } elseif (file_exists('codecept.phar')) {
            $this->options = 'php codecept.phar run';
		} else {
            throw new Shared\TaskException(__CLASS__,"Neither composer nor phar installation of Codeception found");
        }
    }

    public function suite($suite)
    {
        $this->suite = $suite;
        return $this;
    }

    public function test($testName)
    {
        $this->test = $testName;
        return $this;
    }

    public function option($option, $value = "")
    {
        $this->options .= " --$option $value";
        return $this;
    }

    /**
     * set group option. Can be called multiple times
     *
     * @param $group
     * @return $this
     */
    public function group($group)
    {
        $this->options .= " --group $group";
        return $this;
    }

    public function excludeGroup($group)
    {
        $this->options .= " --exclude-group $group";
        return $this;
    }

    /**
     * generate json report
     *
     * @param string $file
     * @return $this
     */
    public function json($file = "")
    {
        $this->options .= " --json $file";
        return $this;
    }

    /**
     * generate xml JUnit report
     *
     * @param string $file
     * @return $this
     */
    public function xml($file = "")
    {
        $this->options .= " --xml $file";
        return $this;
    }

    /**
     * Generate html report
     *
     * @param string $dir
     */
    public function html($dir = "")
    {
        $this->options .= " --html $dir";
        return $this;
    }

    /**
     * generate tap report
     *
     * @param string $file
     * @return $this
     */
    public function tap($file = "")
    {
        $this->options .= " --tap $file";
        return $this;
    }

    /**
     * provides config file other then default `codeception.yml` with `-c` option
     *
     * @param $file
     * @return $this
     */
    public function configFile($file)
    {
        $this->options .= " -c $file";
        return $this;
    }

    /**
     * turn on collecting code coverage
     *
     * @return $this
     */
    public function coverage()
    {
        $this->options .= " --coverage";
        return $this;
    }

    /**
     * execute in silent mode
     *
     * @return $this
     */
    public function silent()
    {
        $this->options .= " --silent";
        return $this;
    }

    /**
     * collect code coverage in xml format. You may pass name of xml file to save results
     *
     * @param string $xml
     * @return $this
     */
    public function coverageXml($xml = "")
    {
        $this->options .= " --coverage-xml $xml";
        return $this;
    }

    /**
     * collect code coverage and generate html report. You may pass
     *
     * @param string $html
     * @return $this
     */
    public function coverageHtml($html = "")
    {
        $this->options .= " --coverage-html $html";
        return $this;
    }

    public function env($env)        
    {
        $this->options .= " --env $env";
        return $this;
    }

    public function debug()
    {
        $this->options .= " --debug";
        return $this;
    }

    public function getCommand()
    {
        return $this->options . " {$this->suite} {$this->test}";
    }

    public function run()
    {
        $command = $this->getCommand();
        $this->printTaskInfo('Executing '. $command);
        return $this->executeCommand($command);
    }

} 