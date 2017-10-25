<?php
use AspectMock\Test as test;
use Robo\Robo;

class ApiGenTest extends \Codeception\TestCase\Test
{
    protected $container;

    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $apigen;

    protected function _before()
    {
        $this->apigen = test::double('Robo\Task\ApiGen\ApiGen', [
            'executeCommand' => null,
            'output' => new \Symfony\Component\Console\Output\NullOutput(),
            'logger' => new \Psr\Log\NullLogger(),
        ]);

        $this->container = Robo::getContainer();
    }

    // tests
    public function testPHPUnitCommand()
    {
        // need an explicit Traversable
        $skippedPaths = new \SplDoublyLinkedList();
        $skippedPaths->push('a');
        $skippedPaths->push('b');

        // going for 'bang for the buck' here re: test converage
        $task = (new \Robo\Task\ApiGen\ApiGen('apigen'))
            ->config('./apigen.neon')
            ->source('src') // single string value of Traversable
            ->extensions('php') // single string value of List
            ->exclude(array('test', 'tmp')) // array value of Traversable
            ->skipDocPath($skippedPaths) // multi-value of Traversable
            ->charset(array('utf8','iso88591')) // array of List
            ->internal('no') // boolean as supported "no"
            ->php(true) // boolean as boolean
            ->tree('Y') // boolean as string
            ->debug('n');

        $linuxCmd = 'apigen generate --config ./apigen.neon --source src --extensions php --exclude test --exclude tmp --skip-doc-path a --skip-doc-path b --charset \'utf8,iso88591\' --internal no --php yes --tree yes --debug no';

        $winCmd = 'apigen generate --config ./apigen.neon --source src --extensions php --exclude test --exclude tmp --skip-doc-path a --skip-doc-path b --charset "utf8,iso88591" --internal no --php yes --tree yes --debug no';

        $cmd = stripos(PHP_OS, 'WIN') === 0 ? $winCmd : $linuxCmd;

        verify($task->getCommand())->equals($cmd);

        $task->run();
        $this->apigen->verifyInvoked('executeCommand', [$cmd]);
    }
}
