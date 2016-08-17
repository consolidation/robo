<?php

use AspectMock\Test as test;
use Robo\Robo;
use Robo\Result;

class SvnTest extends \Codeception\TestCase\Test
{
    protected $container;

    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $svn;

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
        $this->container = Robo::getContainer();
        $this->container->addServiceProvider(\Robo\Task\Vcs\loadTasks::getVcsServices());

        $progressBar = test::double('Symfony\Component\Console\Helper\ProgressBar');
        $nullOutput = new \Symfony\Component\Console\Output\NullOutput();

        $progressIndicator = new \Robo\Common\ProgressIndicator($progressBar, $nullOutput);

        $this->svn = test::double('Robo\Task\Vcs\SvnStack', [
            'executeCommand' => function($process) use($me) { return $me->recordExecuteCommand($process->getCommandLine()); },
            'getOutput' => $nullOutput,
            'logger' => $this->container->get('logger'),
            'getConfig' => $this->container->get('config'),
            'progressIndicator' => $progressIndicator,
        ]);
    }

    // tests
    public function testSvnStackRun()
    {
        $this->svn->construct()->update()->add()->run();
        $this->assertContains('svn update && svn add', $this->commands);
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
