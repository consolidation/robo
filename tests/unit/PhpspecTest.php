<?php
use AspectMock\Test as test;

class PhpspecTest extends \Codeception\TestCase\Test
{
    use \Robo\Task\Phpspec;
    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $phpspec;

    protected function _before()
    {
        $this->phpspec = test::double('Robo\Task\PhpspecTask', [
            'executeCommand' => null,
            'getOutput' => new \Symfony\Component\Console\Output\NullOutput()
        ]);
    }

    // tests
    public function testPhpSpecRun()
    {
        $this->taskPhpspec('phpspec')->run();
        $this->phpspec->verifyInvoked('executeCommand', ['phpspec run']);
    }

    public function testPHPSpecCommand()
    {
        $task = $this->taskPhpspec('phpspec')
            ->stopOnFailure()
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