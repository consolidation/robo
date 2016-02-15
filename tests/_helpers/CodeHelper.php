<?php
namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Robo\Config;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class CodeHelper extends \Codeception\Module
{
    protected static $testPrinter;
    protected static $capturedOutput;

    public function _before(\Codeception\TestCase $test)
    {
        static::$capturedOutput = '';
        static::$testPrinter = new BufferedOutput(OutputInterface::VERBOSITY_DEBUG);
        $testLogger = new \Robo\Common\Logger(static::$testPrinter);

        Config::setOutput(static::$testPrinter);
        Config::setService('logger', $testLogger);
    }

    public function _after(\Codeception\TestCase $test)
    {
        \AspectMock\Test::clean();
        $consoleOutput = new ConsoleOutput();
        Config::setOutput($consoleOutput);
        Config::setService('logger', new \Robo\Common\Logger($consoleOutput));
    }

    public function accumulate()
    {
        static::$capturedOutput .= static::$testPrinter->fetch();
        return static::$capturedOutput;
    }

    public function seeInOutput($value)
    {
        $output = $this->accumulate();
        $this->assertContains($value, $output);
    }

    public function seeOutputEquals($value)
    {
        $output = $this->accumulate();
        $this->assertEquals($value, $output);
    }
}
