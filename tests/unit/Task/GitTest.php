<?php

use AspectMock\Test as test;
use Robo\Robo;

class GitTest extends \Codeception\TestCase\Test
{
    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $git;

    protected function _before()
    {
        $this->git = test::double('Robo\Task\Vcs\GitStack', [
            'executeCommand' => new \AspectMock\Proxy\Anything(),
            'output' => new \Symfony\Component\Console\Output\NullOutput(),
            'logger' => new \Psr\Log\NullLogger(),
        ]);
    }

    // tests
    public function testGitStackRun()
    {
        (new \Robo\Task\Vcs\GitStack('git'))->stopOnFail()->add('-A')->pull()->run();
        $this->git->verifyInvoked('executeCommand', ['git add -A']);
        $this->git->verifyInvoked('executeCommand', ['git pull']);

        (new \Robo\Task\Vcs\GitStack('git'))->add('-A')->pull()->run();
        $this->git->verifyInvoked('executeCommand', ['git add -A && git pull']);
    }

    public function testGitStackCommands()
    {
        verify(
            (new \Robo\Task\Vcs\GitStack())
                ->cloneRepo('http://github.com/consolidation-org/Robo')
                ->pull()
                ->add('-A')
                ->commit('changed')
                ->push()
                ->tag('0.6.0')
                ->push('origin', '0.6.0')
                ->getCommand()
        )->equals("git clone http://github.com/consolidation-org/Robo && git pull && git add -A && git commit -m 'changed' && git push && git tag 0.6.0 && git push origin 0.6.0");
    }

    public function testGitStackCommandsWithTagMessage()
    {
        verify(
            (new \Robo\Task\Vcs\GitStack())
                ->cloneRepo('http://github.com/consolidation-org/Robo')
                ->pull()
                ->add('-A')
                ->commit('changed')
                ->push()
                ->tag('0.6.0', 'message')
                ->push('origin', '0.6.0')
                ->getCommand()
        )->equals("git clone http://github.com/consolidation-org/Robo && git pull && git add -A && git commit -m 'changed' && git push && git tag -m 'message' 0.6.0 && git push origin 0.6.0");
    }
}
