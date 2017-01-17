<?php
use AspectMock\Test as test;

class BehatTest extends \Codeception\TestCase\Test
{
    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $behat;

    protected function _before()
    {
        $this->behat = test::double('Robo\Task\Testing\Behat', [
            'executeCommand' => null,
            'output' => new \Symfony\Component\Console\Output\NullOutput(),
            'logger' => new \Psr\Log\NullLogger(),
        ]);
    }

    // tests
    public function testBehatRun()
    {
        $behat = test::double('Robo\Task\Testing\Behat', ['executeCommand' => null, 'getConfig' => new \Robo\Config(), 'logger' => new \Psr\Log\NullLogger()]);

        (new \Robo\Task\Testing\Behat('behat'))->run();
        $behat->verifyInvoked('executeCommand');
    }

    public function testBehatCommand()
    {
        $behat = test::double('Robo\Task\Testing\Behat', ['executeCommand' => null, 'getConfig' => new \Robo\Config(), 'logger' => new \Psr\Log\NullLogger()]);

        $task = (new \Robo\Task\Testing\Behat('behat'))
            ->stopOnFail()
            ->noInteraction()
            ->colors();
        verify($task->getCommand())->equals('behat --stop-on-failure --no-interaction --colors');
        $task->run();
        $behat->verifyInvoked('executeCommand', ['behat --stop-on-failure --no-interaction --colors']);
    }

}
