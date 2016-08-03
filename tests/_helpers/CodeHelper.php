<?php
namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Robo\Robo;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class CodeHelper extends \Codeception\Module
{
    protected static $testPrinter;
    protected static $capturedOutput;
    protected static $container;

    public function _before(\Codeception\TestCase $test)
    {
        static::$capturedOutput = '';
        static::$testPrinter = new BufferedOutput(OutputInterface::VERBOSITY_DEBUG);
        $progressBar = new \Symfony\Component\Console\Helper\ProgressBar(static::$testPrinter);

        static::$container = new \League\Container\Container();
        \Robo\Runner::configureContainer(static::$container, null, static::$testPrinter);
        Robo::setContainer(static::$container);
        static::$container->add('output', static::$testPrinter);
        static::$container->add('progressBar', $progressBar);
        static::$container->add('progressIndicator', new \Robo\Common\ProgressIndicator($progressBar, static::$testPrinter));
    }

    public function _after(\Codeception\TestCase $test)
    {
        \AspectMock\Test::clean();
        $consoleOutput = new ConsoleOutput();
        static::$container->add('output', $consoleOutput);
        static::$container->add('logger', new \Consolidation\Log\Logger($consoleOutput));
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

    public function doNotSeeInOutput($value)
    {
        $output = $this->accumulate();
        $this->assertNotContains($value, $output);
    }

    public function seeOutputEquals($value)
    {
        $output = $this->accumulate();
        $this->assertEquals($value, $output);
    }
}
