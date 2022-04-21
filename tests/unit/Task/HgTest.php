<?php

use AspectMock\Test as test;

class HgTest extends \Codeception\TestCase\Test
{
    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $hg;

    /**
     * @var \Robo\Task\Vcs\HgStack
     */
    protected $hgStack;

    protected function _before()
    {
        $this->hg = Test::double('Robo\Task\Vcs\HgStack', [
            'executeCommand' => new \AspectMock\Proxy\Anything(),
            'output' => new \Symfony\Component\Console\Output\NullOutput(),
            'logger' => new \Psr\Log\NullLogger(),
        ]);
        $this->hgStack = (new \Robo\Task\Vcs\HgStack('hg'));
    }

    // tests
    public function testHgStackRun()
    {
        $this->hgStack->stopOnFail()->add()->pull()->run();
        $this->hg->verifyInvoked('executeCommand', ['hg add']);
        $this->hg->verifyInvoked('executeCommand', ['hg pull']);

        (new \Robo\Task\Vcs\HgStack('hg'))->add()->pull()->run();
        $this->hg->verifyInvoked('executeCommand', ['hg add && hg pull']);
    }

    public function testHgStackPull()
    {
        $this->assertEquals(
            'hg pull',
            $this->hgStack
                ->pull()
                ->getCommand()
        );
    }

    public function testHgStackAddFiles()
    {
        $this->assertEquals(
            'hg add -I *.php -X *.css',
            $this->hgStack
                ->add('*.php', '*.css')
                ->getCommand()
        );
    }

    public function testHgStackCommands()
    {
        $expected = implode(' ', [
            'hg clone https://bitbucket.org/durin42/hgsubversion',
            '&&',
            'hg pull',
            '&&',
            'hg add',
            '&&',
            "hg commit -m 'changed'",
            '&&',
            'hg push',
            '&&',
            'hg tag 0.6.0',
            '&&',
            "hg push -b '0.6.0'",
        ]);
        $this->assertEquals(
            $expected,
            $this->hgStack
                ->cloneRepo('https://bitbucket.org/durin42/hgsubversion')
                ->pull()
                ->add()
                ->commit('changed')
                ->push()
                ->tag('0.6.0')
                ->push('0.6.0')
                ->getCommand()
        );
    }

    public function testHgStackCommandsWithTagMessage()
    {
        $expected = implode(' ', [
            'hg clone https://bitbucket.org/durin42/hgsubversion',
            '&&',
            'hg pull',
            '&&',
            'hg add',
            '&&',
            "hg commit -m 'changed'",
            '&&',
            'hg push',
            '&&',
            "hg tag -m 'message' 0.6.0".
            '&&',
            "hg push -b '0.6.0'",
        ]);
        $this->assertEquals(
            $expected,
            $this->hgStack
                ->cloneRepo('https://bitbucket.org/durin42/hgsubversion')
                ->pull()
                ->add()
                ->commit('changed')
                ->push()
                ->tag('0.6.0', 'message')
                ->push('0.6.0')
                ->getCommand()
        );
    }
}
