<?php

use AspectMock\Test as test;
use Robo\Config;

class GitTest extends \Codeception\TestCase\Test
{

    protected $container;

    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $git;

    protected function _before()
    {
        $this->git = test::double('Robo\Task\Vcs\GitStack', [
            'executeCommand' => new \AspectMock\Proxy\Anything(),
            'getOutput' => new \Symfony\Component\Console\Output\NullOutput()
        ]);
        $this->container = Config::getContainer();
        $this->container->addServiceProvider(\Robo\Task\Vcs\loadTasks::getVcsServices());
    }

    // tests
    public function testGitStackRun()
    {
        $this->container->get('taskGitStack', ['git'])->stopOnFail(true)->add('-A')->pull()->run();
        $this->git->verifyInvoked('executeCommand', ['git add -A']);
        $this->git->verifyInvoked('executeCommand', ['git pull']);

        $this->container->get('taskGitStack', ['git'])->stopOnFail(false)->add('-A')->pull()->run();
        $this->git->verifyInvoked('executeCommand', ['git add -A && git pull']);
    }

    public function testGitStackCommands()
    {
        verify(
            $this->container->get('taskGitStack')
                ->stopOnFail(false)
                ->cloneRepo('http://github.com/Codegyre/Robo')
                ->pull()
                ->add('-A')
                ->commit('changed')
                ->push()
                ->tag('0.6.0')
                ->push('origin', '0.6.0')
                ->getCommand()
        )->equals("git clone http://github.com/Codegyre/Robo && git pull && git add -A && git commit -m 'changed' && git push && git tag 0.6.0 && git push origin 0.6.0");
    }

    public function testGitStackCommandsWithTagMessage()
    {
        verify(
            $this->container->get('taskGitStack')
                ->stopOnFail(false)
                ->cloneRepo('http://github.com/Codegyre/Robo')
                ->pull()
                ->add('-A')
                ->commit('changed')
                ->push()
                ->tag('0.6.0', 'message')
                ->push('origin', '0.6.0')
                ->getCommand()
        )->equals("git clone http://github.com/Codegyre/Robo && git pull && git add -A && git commit -m 'changed' && git push && git tag -m 'message' 0.6.0 && git push origin 0.6.0");
    }

}
