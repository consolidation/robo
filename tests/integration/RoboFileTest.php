<?php

namespace Robo;

use PHPUnit\Framework\TestCase;
use Robo\Traits\TestTasksTrait;

class RoboFileTest extends TestCase
{
    use TestTasksTrait;
    use Task\Base\Tasks;

    /**
     * @var \Robo\Application
     */
    private $app;

    /**
     * @var Consolidation\AnnotatedCommand\AnnotatedCommandFactory
     */
    private $commandFactory;

    /**
     * @var \Robo\RoboFileFixture
     */
    private $roboCommandFileInstance;

    public function setUp(): void
    {
        $container = $this->initTestTasksTrait();
        $this->app = $container->get('application');
        $this->commandFactory = $container->get('commandFactory');
        $this->roboCommandFileInstance = new \Robo\RoboFileFixture();
        $builder = \Robo\Collection\CollectionBuilder::create($container, $this->roboCommandFileInstance);
        $this->roboCommandFileInstance->setBuilder($builder);
        $commandList = $this->commandFactory->createCommandsFromClass($this->roboCommandFileInstance);
        foreach ($commandList as $command) {
            $this->app->add($command);
        }
    }

    /**
     * Expect the same number of commands as public methods defined in the Robofile.
     *
     * The two default commands from symfony (list and help) need to be added to the count.
     */
    public function testNumberOfCommands()
    {
        $reflectionRoboFile = new \ReflectionClass($this->roboCommandFileInstance);
        $expectedCommands = $reflectionRoboFile->getMethods(\ReflectionMethod::IS_PUBLIC);
        // Filter out hooks, setters, getters and methods from parents and traits.
        $expectedCommands = array_filter($expectedCommands, function ($expectedCommandMethod) {
            return
                $expectedCommandMethod->class === 'Robo\RoboFileFixture' &&
                substr($expectedCommandMethod->name, 0, 4) != 'hook' &&
                substr($expectedCommandMethod->name, 0, 3) != 'set' &&
                substr($expectedCommandMethod->name, 0, 3) != 'get';
        });

        // Two more commands are expected that come from symfony console by default.
        $this->assertEquals(count($this->app->all()), count($expectedCommands) + 2);
    }
}
