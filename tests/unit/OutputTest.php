<?php
use AspectMock\Test as test;
use Robo\Common\IO;

class OutputTest extends \Codeception\TestCase\Test
{
    use \Robo\Common\IO {
        say as public;
        yell as public;
        ask as public;
        getOutput as protected;
    }

    protected $expectedAnswer;


    /**
     * @var \CodeGuy
     */
    protected $guy;

    /**
     * @vAspectMock\Proxy\ClassProxyroxy
     */
    protected $dialog;

    protected function _before()
    {
        $this->dialog = new Symfony\Component\Console\Helper\QuestionHelper;
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
        $this->dialog = $this->getMock('\Symfony\Component\Console\Helper\QuestionHelper', ['ask']);
        $this->dialog->expects($this->once())
            ->method('ask');
        $this->ask('What is your name?');
    }
    public function testAskHiddenMethod()
    {
        $this->dialog = $this->getMock('\Symfony\Component\Console\Helper\QuestionHelper', ['ask']);
        $this->dialog->expects($this->once())
            ->method('ask');
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
        $this->dialog->setInputStream($stream);
        return $this->dialog;
    }

}