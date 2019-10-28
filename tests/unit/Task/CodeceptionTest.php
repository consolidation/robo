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
        $this->assertEquals(
            'codecept.phar run',
            trim((new \Robo\Task\Testing\Codecept('codecept.phar'))->getCommand()));
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
        $this->assertEquals(
            'codecept run unit Codeception/Command --group core --env process1 --coverage',
            (new \Robo\Task\Testing\Codecept('codecept'))
            ->suite('unit')
            ->test('Codeception/Command')
            ->group('core')
            ->env('process1')
            ->coverage()
            ->getCommand()
        );

        $failGroupName = 'failed1';
        $this->assertRegExp(
            "|^codecept run tests/unit/Codeception -c ~/Codeception --xml result\\.xml --html --no-rebuild --override ['\"]extensions: config: Codeception\\\\Extension\\\\RunFailed: fail-group: {$failGroupName}['\"]$|",
            (new \Robo\Task\Testing\Codecept('codecept'))
            ->test('tests/unit/Codeception')
            ->configFile('~/Codeception')
            ->xml('result.xml')
            ->html()
            ->noRebuild()
            ->failGroup($failGroupName)
            ->getCommand()
        );

        $this->assertContains(
            ' --debug',
            (new \Robo\Task\Testing\Codecept('codecept.phar'))->debug()->getCommand());
        $this->assertContains(
            ' --silent',
            (new \Robo\Task\Testing\Codecept('codecept.phar'))->silent()->getCommand());
        $this->assertContains(
            ' --skip-group g',
            (new \Robo\Task\Testing\Codecept('codecept.phar'))->excludeGroup('g')->getCommand());
        $this->assertContains(
            '--tap',
            (new \Robo\Task\Testing\Codecept('codecept.phar'))->tap()->getCommand());
        $this->assertContains(
            '--json',
            (new \Robo\Task\Testing\Codecept('codecept.phar'))->json()->getCommand());
        $this->assertContains(
            '--no-rebuild',
            (new \Robo\Task\Testing\Codecept('codecept.phar'))->noRebuild()->getCommand());
        $failGroupName = 'failed2';
        $this->assertRegExp(
            "|--override ['\"]extensions: config: Codeception\\\\Extension\\\\RunFailed: fail-group: {$failGroupName}['\"]|",
            (new \Robo\Task\Testing\Codecept('codecept.phar'))->failGroup($failGroupName)->getCommand());
    }

}
