<?php
use Robo\Robo;

class OutputTest extends \Codeception\TestCase\Test
{
    use \Robo\Common\IO {
        say as public;
        yell as public;
        ask as public;
        output as protected;
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
        $this->setOutput(Robo::service('output'));
    }

    public function testSay()
    {
        $this->say('Hello, world!');
        $this->guy->seeInOutput('>  Hello, world!');
    }

    public function testAskReply()
    {
        $this->expectedAnswer = 'jon';
        verify($this->ask('What is your name?'))->equals('jon');
        $this->guy->seeOutputEquals('?  What is your name? ');
    }
    public function testAskMethod()
    {
        if (method_exists($this, 'createMock')) {
            $this->dialog = $this->createMock('\Symfony\Component\Console\Helper\QuestionHelper', ['ask']);
        } else {
            $this->dialog = $this->getMock('\Symfony\Component\Console\Helper\QuestionHelper', ['ask']);
        }
        $this->dialog->expects($this->once())
            ->method('ask');
        $this->ask('What is your name?');
    }
    public function testAskHiddenMethod()
    {
        if (method_exists($this, 'createMock')) {
            $this->dialog = $this->createMock('\Symfony\Component\Console\Helper\QuestionHelper', ['ask']);
        } else {
            $this->dialog = $this->getMock('\Symfony\Component\Console\Helper\QuestionHelper', ['ask']);
        }
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
        // Use StreamableInputInterface when it's available
        if ($this->input() instanceof \Symfony\Component\Console\Input\StreamableInputInterface) {
            $this->input()->setStream($stream);
        } else {
            // setInputStream deprecated in Symfony 3.2.
            $this->dialog->setInputStream($stream);
        }
        return $this->dialog;
    }
}
