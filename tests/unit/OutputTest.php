<?php
use AspectMock\Test as test;
use Robo\Output;

class OutputTest extends \Codeception\TestCase\Test
{
    use Output {
        say as public;
        yell as public;
        ask as public;
        getOutput as protected;
        getDialog as protected;
    }

    protected $expectedAnswer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $nullOutput;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $nullDialog;

    protected function _before()
    {
        $this->nullOutput = $this->getMock('Symfony\Component\Console\Output\NullOutput', ['writeln','write']);
        $this->nullDialog = new \Symfony\Component\Console\Helper\DialogHelper;
    }

    public function testSay()
    {
        $this->nullOutput->expects($this->once())
            ->method('writeln')
            ->with($this->equalTo('➜  Hello, world!'));

        $this->say('Hello, world!');
    }

    public function testAskReply()
    {
        $this->expectedAnswer = 'jon';
        $this->nullOutput->expects($this->once())
            ->method('write')
            ->with($this->equalTo('<question>?  What is your name?</question> '));

        verify($this->ask('What is your name?'))->equals('jon');
    }

    public function testAskMethod()
    {
        $this->nullDialog = $this->getMock('\Symfony\Component\Console\Helper\DialogHelper', ['ask']);
        $this->nullDialog->expects($this->once())
            ->method('ask');
        $this->ask('What is your name?');
    }

    public function testAskHidden()
    {
        $this->expectedAnswer = 'jon';
        $this->nullOutput->expects($this->once())
            ->method('write')
            ->with($this->equalTo('<question>?  What is your name?</question> '));

        verify($this->ask('What is your name?', false))->equals('jon');
    }

    public function testAskHiddenMethod()
    {
        $this->nullDialog = $this->getMock('\Symfony\Component\Console\Helper\DialogHelper', ['askHiddenResponse']);
        $this->nullDialog->expects($this->once())
            ->method('askHiddenResponse');
        $this->ask('What is your name?', true);
    }
    
    public function testYell()
    {
        $this->nullOutput->expects($this->exactly(3))
            ->method('writeln');
        $this->yell('Buuuu!');
    }

    protected function getOutput()
    {
        return $this->nullOutput;
    }

    protected function getDialog()
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $this->expectedAnswer);
        rewind($stream);

        $this->nullDialog->setInputStream($stream);
        return $this->nullDialog;
    }
}