<?php
namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Robo\Config;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;

class CodeHelper extends \Codeception\Module
{

    public function _before(\Codeception\TestCase $test)
    {
        TestPrinter::$output = '';
        Config::setOutput(new TestPrinter());
    }

    public function _after(\Codeception\TestCase $test)
    {
        \AspectMock\Test::clean();
        Config::setOutput(new ConsoleOutput());
    }

    public function seeInOutput($value)
    {
        $this->assertContains($value, TestPrinter::$output);
    }

    public function seeOutputEquals($value)
    {
        $this->assertEquals($value, TestPrinter::$output);
    }
}

class TestPrinter extends NullOutput {

    static $output = '';

    public function writeln($messages, $type = self::OUTPUT_NORMAL)
    {
        static::$output .= $messages."\n";
    }

    public function write($messages, $newline = false, $type = self::OUTPUT_NORMAL)
    {
        static::$output .= $messages;
    }

}