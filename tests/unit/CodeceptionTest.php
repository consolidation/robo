<?php
use AspectMock\Test as test;

class CodeceptionTest extends \Codeception\TestCase\Test
{
    use \Robo\Task\Testing\loadTasks;
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
    }

    // tests
    public function testCodeceptionCommand()
    {
        verify($this->taskCodecept()->getCommand())->equals($this->command);
        verify(trim($this->taskCodecept('codecept.phar')->getCommand()))->equals('codecept.phar run');
    }

    public function testCodeceptionRun()
    {
        $this->taskCodecept()->run();
        $this->codecept->verifyInvoked('executeCommand', [$this->command]);
    }

    public function testCodeceptOptions()
    {
        verify($this->taskCodecept('codecept')
            ->suite('unit')
            ->test('Codeception/Command')
            ->group('core')
            ->env('process1')
            ->coverage()
            ->getCommand()
        )->equals('codecept run --group core --env process1 --coverage unit Codeception/Command');

        verify($this->taskCodecept('codecept')
            ->test('tests/unit/Codeception')
            ->configFile('~/Codeception')
            ->xml('result.xml')
            ->html()
            ->getCommand()
        )->equals('codecept run -c ~/Codeception --xml result.xml --html tests/unit/Codeception');

        verify($this->taskCodecept()->debug()->getCommand())->contains(' --debug');
        verify($this->taskCodecept()->silent()->getCommand())->contains(' --silent');
        verify($this->taskCodecept()->excludeGroup('g')->getCommand())->contains(' --skip-group g');
        verify($this->taskCodecept()->tap()->getCommand())->contains('--tap');
        verify($this->taskCodecept()->json()->getCommand())->contains('--json');
    }

}