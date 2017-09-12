<?php
use AspectMock\Test as test;

class CodeceptionTest extends \Codeception\TestCase\Test
{
    protected $container;

    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $codecept;

    protected function _before()
    {
        $this->codecept = test::double('Robo\Task\Testing\Codecept', [
            'executeCommand' => null,
            'output' => new \Symfony\Component\Console\Output\NullOutput()
        ]);
    }

    // tests
    public function testCodeceptionCommand()
    {
        verify(trim((new \Robo\Task\Testing\Codecept('codecept.phar'))->getCommand()))->equals('codecept.phar run');
    }

    public function testCodeceptionRun()
    {
        $task = new \Robo\Task\Testing\Codecept('codecept.phar');
        $task->setLogger(new \Psr\Log\NullLogger());

        $task->run();
        $this->codecept->verifyInvoked('executeCommand');
    }

    public function testCodeceptOptions()
    {
        verify((new \Robo\Task\Testing\Codecept('codecept'))
            ->suite('unit')
            ->test('Codeception/Command')
            ->group('core')
            ->env('process1')
            ->coverage()
            ->getCommand()
        )->equals('codecept run unit Codeception/Command --group core --env process1 --coverage');

        $failGroupName = 'failed1';
        verify((new \Robo\Task\Testing\Codecept('codecept'))
            ->test('tests/unit/Codeception')
            ->configFile('~/Codeception')
            ->xml('result.xml')
            ->html()
            ->noRebuild()
            ->failGroup($failGroupName)
            ->getCommand()
        )->regExp("|^codecept run tests/unit/Codeception -c ~/Codeception --xml result\\.xml --html --no-rebuild --override ['\"]extensions: config: Codeception\\\\Extension\\\\RunFailed: fail-group: {$failGroupName}['\"]$|");

        verify((new \Robo\Task\Testing\Codecept('codecept.phar'))->debug()->getCommand())->contains(' --debug');
        verify((new \Robo\Task\Testing\Codecept('codecept.phar'))->silent()->getCommand())->contains(' --silent');
        verify((new \Robo\Task\Testing\Codecept('codecept.phar'))->excludeGroup('g')->getCommand())->contains(' --skip-group g');
        verify((new \Robo\Task\Testing\Codecept('codecept.phar'))->tap()->getCommand())->contains('--tap');
        verify((new \Robo\Task\Testing\Codecept('codecept.phar'))->json()->getCommand())->contains('--json');
        verify((new \Robo\Task\Testing\Codecept('codecept.phar'))->noRebuild()->getCommand())->contains('--no-rebuild');
        $failGroupName = 'failed2';
        verify((new \Robo\Task\Testing\Codecept('codecept.phar'))->failGroup($failGroupName)->getCommand())
            ->regExp("|--override ['\"]extensions: config: Codeception\\\\Extension\\\\RunFailed: fail-group: {$failGroupName}['\"]|");
    }

}
