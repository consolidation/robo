<?php

use AspectMock\Test as test;
use Robo\Robo;
use Robo\Result;

class HgTest extends \Codeception\TestCase\Test
{

    /**
     * @var \League\Container\Container
     */
    protected $container;

    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $hg;

    /**
     * @var \Robo\Task\Vcs\HgStack
     */
    protected $hgStack;

    /**
     * @var array
     */
    protected $commands = [];

    public function recordExecuteCommand($cmd)
    {
        $this->commands[] = $cmd;
        return Result::success($this->container->get('taskGitStack'));
    }

    protected function _before()
    {
        $me = $this;
        $this->hg = Test::double('Robo\Task\Vcs\HgStack', [
            'executeCommand' => function($process) use($me) { return $me->recordExecuteCommand($process->getCommandLine()); },
            'getOutput' => new \Symfony\Component\Console\Output\NullOutput()
        ]);
        $this->container = Robo::getContainer();
        $this->container->addServiceProvider(\Robo\Task\Vcs\loadTasks::getVcsServices());
        $this->hgStack = $this->container->get('taskHgStack', ['hg']);
    }

    // tests
    public function testHgStackRun()
    {
        $this->hgStack->stopOnFail()->add()->pull()->run();
        $this->assertContains('hg add', $this->commands);
        $this->assertContains('hg pull', $this->commands);

        $this->container->get('taskHgStack', ['hg'])->add()->pull()->run();
        $this->assertContains('hg add && hg pull', $this->commands);
    }

    public function testHgStackPull()
    {
        verify(
            $this->hgStack
                ->pull()
                ->getCommand()
        )->equals('hg pull');
    }

    public function testHgStackAddFiles()
    {
        verify(
            $this->hgStack
                ->add('*.php', '*.css')
                ->getCommand()
        )->equals('hg add -I *.php -X *.css');
    }

    public function testHgStackCommands()
    {
        verify(
            $this->hgStack
                ->cloneRepo('https://bitbucket.org/durin42/hgsubversion')
                ->pull()
                ->add()
                ->commit('changed')
                ->push()
                ->tag('0.6.0')
                ->push('0.6.0')
                ->getCommand()
        )->equals("hg clone https://bitbucket.org/durin42/hgsubversion && hg pull && hg add && hg commit -m 'changed' && hg push && hg tag 0.6.0 && hg push -b '0.6.0'");
    }

    public function testHgStackCommandsWithTagMessage()
    {
        verify(
            $this->hgStack
                ->cloneRepo('https://bitbucket.org/durin42/hgsubversion')
                ->pull()
                ->add()
                ->commit('changed')
                ->push()
                ->tag('0.6.0', 'message')
                ->push('0.6.0')
                ->getCommand()
        )->equals("hg clone https://bitbucket.org/durin42/hgsubversion && hg pull && hg add && hg commit -m 'changed' && hg push && hg tag -m 'message' 0.6.0 && hg push -b '0.6.0'");
    }
}
