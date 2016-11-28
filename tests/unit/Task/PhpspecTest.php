<?php
use AspectMock\Test as test;

class PhpspecTest extends \Codeception\TestCase\Test
{
    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $phpspec;

    protected function _before()
    {
        $this->phpspec = test::double('Robo\Task\Testing\Phpspec', [
            'executeCommand' => null,
            'output' => new \Symfony\Component\Console\Output\NullOutput(),
            'logger' => new \Psr\Log\NullLogger(),
        ]);
    }

    // tests
    public function testPhpSpecRun()
    {
        (new \Robo\Task\Testing\Phpspec('phpspec'))->run();
        $this->phpspec->verifyInvoked('executeCommand', ['phpspec run']);
    }

    public function testPHPSpecCommand()
    {
        $task = (new \Robo\Task\Testing\Phpspec('phpspec'))
            ->stopOnFail()
            ->noCodeGeneration()
            ->quiet()
            ->verbose('vv')
            ->noAnsi()
            ->noInteraction()
            ->format('pretty');
        verify($task->getCommand())->equals('phpspec run --stop-on-failure --no-code-generation --quiet -vv --no-ansi --no-interaction --format pretty');
        $task->run();
        $this->phpspec->verifyInvoked('executeCommand', ['phpspec run --stop-on-failure --no-code-generation --quiet -vv --no-ansi --no-interaction --format pretty']);
    }
}
