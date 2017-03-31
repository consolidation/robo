<?php
use AspectMock\Test as test;

class DockerTest extends \Codeception\TestCase\Test
{
    protected $container;

    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $baseDocker;

    protected function _before()
    {
        $this->baseDocker = test::double('Robo\Task\Docker\Base', [
            'output' => new \Symfony\Component\Console\Output\NullOutput(),
            'logger' => new \Psr\Log\NullLogger(),
        ]);
    }
    // tests
    public function testDockerBuild()
    {
        $docker = test::double('Robo\Task\Docker\Build', ['executeCommand' => null, 'getConfig' => new \Robo\Config(), 'logger' => new \Psr\Log\NullLogger()]);

        (new \Robo\Task\Docker\Build())->run();
        $docker->verifyInvoked('executeCommand', ['docker build  .']);

        (new \Robo\Task\Docker\Build())->tag('something')->run();
        $docker->verifyInvoked('executeCommand', ['docker build  -t something .']);
    }

    public function testDockerCommit()
    {
        $docker = test::double('Robo\Task\Docker\Commit', ['executeCommand' => null, 'getConfig' => new \Robo\Config(), 'logger' => new \Psr\Log\NullLogger()]);

        (new \Robo\Task\Docker\Commit('cid'))->run();
        $docker->verifyInvoked('executeCommand', ['docker commit cid  ']);

        (new \Robo\Task\Docker\Commit('cid'))->name('somename')->run();
        $docker->verifyInvoked('executeCommand', ['docker commit cid somename ']);
    }

    public function testDockerExec()
    {
        $docker = test::double('Robo\Task\Docker\Exec', ['executeCommand' => null, 'getConfig' => new \Robo\Config(), 'logger' => new \Psr\Log\NullLogger()]);

        (new \Robo\Task\Docker\Exec('cid'))->run();
        $docker->verifyInvoked('executeCommand', ['docker exec  cid ']);

        (new \Robo\Task\Docker\Exec('cid'))->exec('pwd')->run();
        $docker->verifyInvoked('executeCommand', ['docker exec  cid pwd']);
    }

    public function testDockerPull()
    {
        $docker = test::double('Robo\Task\Docker\Pull', ['executeCommand' => null, 'getConfig' => new \Robo\Config(), 'logger' => new \Psr\Log\NullLogger()]);

        (new \Robo\Task\Docker\Pull('image'))->run();
        $docker->verifyInvoked('executeCommand', ['docker pull image  ']);
    }

    public function testDockerRemove()
    {
        $docker = test::double('Robo\Task\Docker\Remove', ['executeCommand' => null, 'getConfig' => new \Robo\Config(), 'logger' => new \Psr\Log\NullLogger()]);

        (new \Robo\Task\Docker\Remove('container'))->run();
        $docker->verifyInvoked('executeCommand', ['docker rm container  ']);
    }

    public function testDockerRun()
    {
        $docker = test::double('Robo\Task\Docker\Run', ['executeCommand' => null, 'getConfig' => new \Robo\Config(), 'logger' => new \Psr\Log\NullLogger(), 'getUniqId' => '12345']);

        (new \Robo\Task\Docker\Run('cid'))->tmpDir('/tmp')->run();
        $docker->verifyInvoked('executeCommand', ['docker run  -i --cidfile /tmp/docker_12345.cid cid']);

        (new \Robo\Task\Docker\Run('cid'))->tmpDir('/tmp')->exec('pwd')->run();
        $docker->verifyInvoked('executeCommand', ['docker run  -i --cidfile /tmp/docker_12345.cid cid pwd']);
    }

    public function testDockerStart()
    {
        $docker = test::double('Robo\Task\Docker\Start', ['executeCommand' => null, 'getConfig' => new \Robo\Config(), 'logger' => new \Psr\Log\NullLogger()]);

        (new \Robo\Task\Docker\Start('cid'))->run();
        $docker->verifyInvoked('executeCommand', ['docker start  cid']);
    }

    public function testDockerStop()
    {
        $docker = test::double('Robo\Task\Docker\Stop', ['executeCommand' => null, 'getConfig' => new \Robo\Config(), 'logger' => new \Psr\Log\NullLogger()]);

        (new \Robo\Task\Docker\Stop('cid'))->run();
        $docker->verifyInvoked('executeCommand', ['docker stop  cid']);
    }
}
