<?php

use AspectMock\Test as test;

class SvnTest extends \Codeception\TestCase\Test
{
    use \Robo\Task\Svn;
    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $svn;

    protected function _before()
    {
        $this->svn = test::double('Robo\Task\SvnStackTask', [
            'taskExec' => new \AspectMock\Proxy\Anything(),
            'getOutput' => new \Symfony\Component\Console\Output\NullOutput()
        ]);
    }

    // tests
    public function testSvnStackRun()
    {
        $this->taskSvnStack('svn')->update()->add()->run();
        $this->svn->verifyInvoked('taskExec', ['svn add ']);
        $this->svn->verifyInvoked('taskExec', ['svn update ']);
    }

    public function testSvnStackCommands()
    {
        verify(
            $this->taskSvnStack('svn', 'guest', 'foo')
                ->checkout('svn://server/trunk')
                ->update()
                ->add()
                ->commit('changed')
                ->getCommand()
        )->equals("svn --username guest --password foo checkout svn://server/trunk && svn --username guest --password foo update  && svn --username guest --password foo add  && svn --username guest --password foo commit -m 'changed' ");
    }

}
