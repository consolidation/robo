<?php
namespace Robo\Tests;

use PHPUnit_Framework_TestCase;
use Mockery as m;

class OutputTest extends PHPUnit_Framework_TestCase {

    private $output;
    private $console;

    const SAY_PREFIX = '';

    public function setUp()
    {
        $this->output = new OutputStub;
        $this->console = m::mock('Symfony\Component\Console\Output\ConsoleOutput');
        OutputStub::setOutput($this->console);
    }

    public function testSay()
    {
        $this->console->shouldReceive('writeln')
            ->with('➜  Hello, world!')
            ->once();

        $this->output->say('Hello, world!');
    }

    public function testAsk()
    {
        // Mock DialogHelper
        $dialog = m::mock('Symfony\Component\Console\Helper\DialogHelper');
        $dialog->shouldReceive('ask')
            ->with($this->console, '<question>?  What is your name?</question> ');

        OutputStub::setDialogHelper($dialog);

        $result = $this->output->ask('What is your name?');
    }
}
