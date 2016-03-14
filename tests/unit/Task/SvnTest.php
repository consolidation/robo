<?php

use AspectMock\Test as test;
use Robo\Runner;
use Robo\Container\RoboContainer;

class SvnTest extends \Codeception\TestCase\Test
{
    protected $container;

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

        $this->container = new RoboContainer();
        Runner::configureContainer($this->container);
        $this->container->addServiceProvider(\Robo\Task\Vcs\ServiceProvider::class);
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
            $this->container->get('taskSvnStack', ['guest', 'foo'])
                ->checkout('svn://server/trunk')
                ->update()
                ->add()
                ->commit('changed')
                ->getCommand()
        )->equals("svn --username guest --password foo checkout svn://server/trunk && svn --username guest --password foo update && svn --username guest --password foo add && svn --username guest --password foo commit -m 'changed'");
    }

}
