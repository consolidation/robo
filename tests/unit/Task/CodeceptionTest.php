<?php
use AspectMock\Test as test;
use Robo\Config;

class CodeceptionTest extends \Codeception\TestCase\Test
{
    protected $container;

    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $codecept;

    /**
     * @var string
     */
    protected $command;

    protected function _before()
    {
        $isWindows = defined('PHP_WINDOWS_VERSION_MAJOR');
        $this->command = $isWindows ? 'call vendor/bin/codecept run' : 'vendor/bin/codecept run';
        $this->codecept = test::double('Robo\Task\Testing\Codecept', [
            'executeCommand' => null,
            'getOutput' => new \Symfony\Component\Console\Output\NullOutput()
        ]);
        $this->container = Config::getContainer();
        $this->container->addServiceProvider(\Robo\Task\Testing\ServiceProvider::class);
    }

    // tests
    public function testCodeceptionCommand()
    {
        verify($this->container->get('taskCodecept')->getCommand())->equals($this->command);
        verify(trim($this->container->get('taskCodecept', ['codecept.phar'])->getCommand()))->equals('codecept.phar run');
    }

    public function testCodeceptionRun()
    {
        $this->container->get('taskCodecept')->run();
        $this->codecept->verifyInvoked('executeCommand', [$this->command]);
    }

    public function testCodeceptOptions()
    {
        verify($this->container->get('taskCodecept', ['codecept'])
            ->suite('unit')
            ->test('Codeception/Command')
            ->group('core')
            ->env('process1')
            ->coverage()
            ->getCommand()
        )->equals('codecept run --group core --env process1 --coverage unit Codeception/Command');

        verify($this->container->get('taskCodecept', ['codecept'])
            ->test('tests/unit/Codeception')
            ->configFile('~/Codeception')
            ->xml('result.xml')
            ->html()
            ->getCommand()
        )->equals('codecept run -c ~/Codeception --xml result.xml --html tests/unit/Codeception');

        verify($this->container->get('taskCodecept')->debug()->getCommand())->contains(' --debug');
        verify($this->container->get('taskCodecept')->silent()->getCommand())->contains(' --silent');
        verify($this->container->get('taskCodecept')->excludeGroup('g')->getCommand())->contains(' --skip-group g');
        verify($this->container->get('taskCodecept')->tap()->getCommand())->contains('--tap');
        verify($this->container->get('taskCodecept')->json()->getCommand())->contains('--json');
    }

}
