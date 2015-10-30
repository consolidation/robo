<?php
use AspectMock\Test as test;

class ApiGenTest extends \Codeception\TestCase\Test
{
    use \Robo\Task\ApiGen\loadTasks;
    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $apigen;

    protected function _before()
    {
        $this->apigen = test::double('Robo\Task\ApiGen\ApiGen', [
            'executeCommand' => null,
            'getOutput' => new \Symfony\Component\Console\Output\NullOutput()
        ]);
    }

    // tests
    public function testPHPUnitCommand()
    {
        // need an explicit Traversable
        $skippedPaths = new \SplDoublyLinkedList();
        $skippedPaths->push('a');
        $skippedPaths->push('b');

        // going for 'bang for the buck' here re: test converage
        $task = $this->taskApiGen('apigen')
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

        $cmd = 'apigen --config ./apigen.neon --source src --extensions php --exclude test --exclude tmp --skip-doc-path a --skip-doc-path b --charset utf8,iso88591 --internal no --php yes --tree yes --debug no';
        verify($task->getCommand())->equals($cmd);

        $task->run();
        $this->apigen->verifyInvoked('executeCommand', [$cmd]);
    }

}
