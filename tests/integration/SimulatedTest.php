<?php
namespace Robo;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use Robo\Traits\TestTasksTrait;

class SimulatedTest extends TestCase
{
    use TestTasksTrait;
    use Collection\Tasks;
    use Task\Filesystem\Tasks;

    protected $fixtures;

    public function setUp(): void
    {
        $this->fixtures = new Fixtures();
        $this->initTestTasksTrait();
        $this->fixtures->createAndCdToSandbox();
    }

    public function tearDown(): void
    {
        $this->fixtures->cleanup();
    }

    public function testSimulateDirCreation()
    {
        // Set up a collection to add tasks to
        $collection = $this->collectionBuilderForTest();
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
        $this->assertFileDoesNotExist('simulatedir/error.txt');
        $this->assertOutputContains('[Simulator] Simulating Filesystem\FilesystemStack()');
    }

}


