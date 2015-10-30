<?php

use AspectMock\Test as test;

class SvnTest extends \Codeception\TestCase\Test
{
    use \Robo\Task\Vcs\loadTasks;
    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $svn;

    protected function _before()
    {
        $this->svn = test::double('Robo\Task\Vcs\SvnStack', [
            'executeCommand' => new \AspectMock\Proxy\Anything(),
            'getOutput' => new \Symfony\Component\Console\Output\NullOutput()
        ]);
    }

    // tests
    public function testSvnStackRun()
    {
        $this->svn->construct()->update()->add()->run();
        $this->svn->verifyInvoked('executeCommand', ['svn update && svn add']);
    }

    public function testSvnStackCommands()
    {
        verify(
            $this->taskSvnStack('guest', 'foo')
                ->checkout('svn://server/trunk')
                ->update()
                ->add()
                ->commit('changed')
                ->getCommand()
        )->equals("svn --username guest --password foo checkout svn://server/trunk && svn --username guest --password foo update && svn --username guest --password foo add && svn --username guest --password foo commit -m 'changed'");
    }

}
