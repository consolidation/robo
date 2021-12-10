<?php

use AspectMock\Test as test;

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
        $linuxCmd = "git clone https://github.com/consolidation-org/Robo && git pull && git add -A && git commit -m 'changed' && git push && git tag 0.6.0 && git push origin 0.6.0";

        $winCmd = 'git clone https://github.com/consolidation-org/Robo && git pull && git add -A && git commit -m "changed" && git push && git tag 0.6.0 && git push origin 0.6.0';

        $cmd = stripos(PHP_OS, 'WIN') === 0 ? $winCmd : $linuxCmd;

        $this->assertEquals(
            $cmd,
            (new \Robo\Task\Vcs\GitStack())
                ->cloneRepo('https://github.com/consolidation-org/Robo')
                ->pull()
                ->add('-A')
                ->commit('changed')
                ->push()
                ->tag('0.6.0')
                ->push('origin', '0.6.0')
                ->getCommand()
        );
    }

    public function testGitStackCommandsWithTagMessage()
    {
        $linuxCmd = "git clone https://github.com/consolidation-org/Robo && git pull && git add -A && git commit -m 'changed' && git push && git tag -m 'message' 0.6.0 && git push origin 0.6.0";

        $winCmd = 'git clone https://github.com/consolidation-org/Robo && git pull && git add -A && git commit -m "changed" && git push && git tag -m \'message\' 0.6.0 && git push origin 0.6.0';

        $cmd = stripos(PHP_OS, 'WIN') === 0 ? $winCmd : $linuxCmd;

        $this->assertEquals(
            $cmd,
            (new \Robo\Task\Vcs\GitStack())
                ->cloneRepo('https://github.com/consolidation-org/Robo')
                ->pull()
                ->add('-A')
                ->commit('changed')
                ->push()
                ->tag('0.6.0', 'message')
                ->push('origin', '0.6.0')
                ->getCommand()
        );
    }

    public function testGitStackShallowCloneCommand()
    {
        $cmd = 'git clone --depth 1 https://github.com/consolidation-org/Robo ./deployment-path';

        $this->assertEquals(
            $cmd,
            (new \Robo\Task\Vcs\GitStack())
                ->cloneShallow('https://github.com/consolidation-org/Robo', './deployment-path')
                ->getCommand()
        );
    }

    public function testGitStackShallowCloneCommandWithDifferentDepth()
    {
        $cmd = 'git clone --depth 3 https://github.com/consolidation-org/Robo . --branch feature';

        $this->assertEquals(
            $cmd,
            (new \Robo\Task\Vcs\GitStack())
                ->cloneShallow('https://github.com/consolidation-org/Robo', '.', 'feature', 3)
                ->getCommand()
        );
    }
}
