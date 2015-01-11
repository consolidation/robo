<?php
namespace Robo\Task\Testing;

use Robo\Task\Shared;
use Robo\Task\Shared\TaskInterface;
use Robo\Task\Shared\CommandInterface;

/**
 * Executes Codeception tests
 *
 * ``` php
 * <?php
 * // config
 * $this->taskCodecept()
 *      ->suite('acceptance')
 *      ->env('chrome')
 *      ->group('admin')
 *      ->xml()
 *      ->html()
 *      ->run();
 *
 * ?>
 * ```
 *
 */
class CodeceptRun implements TaskInterface, CommandInterface
{
    use \Robo\Output;
    use \Robo\Task\Shared\Executable;

    protected $suite = '';
    protected $test = '';
    protected $command;

    public function __construct($pathToCodeception = '')
    {
        if ($pathToCodeception) {
            $this->command = "$pathToCodeception run";
        } elseif (file_exists('vendor/bin/codecept')) {
            $this->command = 'vendor/bin/codecept run';
            if (defined('PHP_WINDOWS_VERSION_BUILD')) {
                $this->command = 'call ' . $this->command;
            }
        } elseif (file_exists('codecept.phar')) {
            $this->command = 'php codecept.phar run';
        } else {
            throw new Shared\TaskException(__CLASS__, "Neither composer nor phar installation of Codeception found");
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

    /**
     * set group option. Can be called multiple times
     *
     * @param $group
     * @return $this
     */
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
     * generate json report
     *
     * @param string $file
     * @return $this
     */
    public function json($file = null)
    {
        $this->option("json", $file);
        return $this;
    }

    /**
     * generate xml JUnit report
     *
     * @param string $file
     * @return $this
     */
    public function xml($file = null)
    {
        $this->option("xml", $file);
        return $this;
    }

    /**
     * Generate html report
     *
     * @param string $dir
     */
    public function html($dir = null)
    {
        $this->option("html", $dir);
        return $this;
    }

    /**
     * generate tap report
     *
     * @param string $file
     * @return $this
     */
    public function tap($file = null)
    {
        $this->option("tap", $file);
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
        $this->option("-c", $file);
        return $this;
    }

    /**
     * turn on collecting code coverage
     *
     * @return $this
     */
    public function coverage()
    {
        $this->option("coverage");
        return $this;
    }

    /**
     * execute in silent mode
     *
     * @return $this
     */
    public function silent()
    {
        $this->option("silent");
        return $this;
    }

    /**
     * collect code coverage in xml format. You may pass name of xml file to save results
     *
     * @param string $xml
     * @return $this
     */
    public function coverageXml($xml = null)
    {
        $this->option("coverage-xml", $xml);
        return $this;
    }

    /**
     * collect code coverage and generate html report. You may pass
     *
     * @param string $html
     * @return $this
     */
    public function coverageHtml($html = null)
    {
        $this->option("coverage-html", $html);
        return $this;
    }

    public function env($env)
    {
        $this->option("env", $env);
        return $this;
    }

    public function debug()
    {
        $this->option("debug");
        return $this;
    }

    public function getCommand()
    {
        $this->option(null, $this->suite)
            ->option(null, $this->test);
        return $this->command . $this->arguments;
    }

    public function run()
    {
        $command = $this->getCommand();
        $this->printTaskInfo('Executing ' . $command);
        return $this->executeCommand($command);
    }

}