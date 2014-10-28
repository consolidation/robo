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
     * @var \CodeGuy
     */
    protected $guy;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $nullDialog;

    protected function _before()
    {
        $this->nullDialog = new \Symfony\Component\Console\Helper\DialogHelper;
    }

    public function testSay()
    {
        $this->say('Hello, world!');
        $char = strncasecmp(PHP_OS, 'WIN', 3) == 0 ? '>' : '➜';
        $this->guy->seeInOutput($char . '  Hello, world!');
    }

    public function testAskReply()
    {
        $this->expectedAnswer = 'jon';
        verify($this->ask('What is your name?'))->equals('jon');
        $this->guy->seeOutputEquals('<question>?  What is your name?</question> ');
    }

    public function testAskMethod()
    {
        $this->nullDialog = $this->getMock('\Symfony\Component\Console\Helper\DialogHelper', ['ask']);
        $this->nullDialog->expects($this->once())
            ->method('ask');
        $this->ask('What is your name?');
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
        $this->yell('Buuuu!');
        $this->guy->seeInOutput('Buuuu!');
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