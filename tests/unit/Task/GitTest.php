<?php

use AspectMock\Test as test;
use Robo\Robo;
use Robo\Result;

class GitTest extends \Codeception\TestCase\Test
{

    protected $container;

    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $git;
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
        // $user = test::double(new User, ['getName' => function() { return $this->login; }]);
        $this->git = test::double('Robo\Task\Vcs\GitStack', [
            'executeCommand' => function($process) use($me) { return $me->recordExecuteCommand($process->getCommandLine()); },
            'getOutput' => new \Symfony\Component\Console\Output\NullOutput()
        ]);
        $this->container = Robo::getContainer();
        $this->container->addServiceProvider(\Robo\Task\Vcs\loadTasks::getVcsServices());
    }

    // tests
    public function testGitStackRun()
    {
        $this->container->get('taskGitStack', ['git'])->stopOnFail()->add('-A')->pull()->run();
        $this->assertContains('git add -A', $this->commands);
        $this->assertContains('git pull', $this->commands);

        $this->container->get('taskGitStack', ['git'])->add('-A')->pull()->run();
        $this->assertContains('git add -A && git pull', $this->commands);
    }

    public function testGitStackCommands()
    {
        verify(
            $this->container->get('taskGitStack')
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
            $this->container->get('taskGitStack')
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
