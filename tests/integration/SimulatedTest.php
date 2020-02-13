<?php
namespace Robo;

use PHPUnit\Framework\TestCase;
use Robo\Traits\TestTasksTrait;

class SimulatedTest extends TestCase
{
    use TestTasksTrait;
    use Collection\loadTasks;
    use Task\Filesystem\loadTasks;

    protected $fixtures;

    public function setUp()
    {
        $this->fixtures = new Fixtures();
        $this->initTestTasksTrait();
        $this->fixtures->createAndCdToSandbox();
    }

    public function tearDown()
    {
        $this->fixtures->cleanup();
    }

    public function testSimulateDirCreation()
    {
        // Set up a collection to add tasks to
        $collection = $this->collectionBuilder();
        $collection->simulated(true);

        // Set up a filesystem stack
        $collection->taskFilesystemStack()
            ->mkdir('simulatedir')
            ->touch('simulatedir/error.txt');

        // Run the task collection; the files would be present were this
        // operation not simulated.
        $result = $collection->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        // Nothing should be created in simulated mode
        $this->assertFileNotExists('simulatedir/error.txt');
        $this->assertOutputContains('[Simulator] Simulating Filesystem\FilesystemStack()');
    }

}


