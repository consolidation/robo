<?php
namespace Robo;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use Robo\Traits\TestTasksTrait;

class FilesystemStackTest extends TestCase
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

    public function testDirAndFileCreation()
    {
        // Set up a collection to add tasks to
        $collection = $this->collectionBuilderForTest();

        // Set up a filesystem stack
        $collection->taskFilesystemStack()
            ->mkdir('simulatedir')
            ->touch('simulatedir/error.txt');

        $this->assertFileDoesNotExist('simulatedir/error.txt');

        // Run the task collection; the files should be present afterwards
        $result = $collection->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('simulatedir/error.txt');
    }

    public function testCreateDir()
    {
        $this->assertFileDoesNotExist('log/error.txt');
        $result = $this->taskFilesystemStack()
            ->mkdir('log')
            ->touch('log/error.txt')
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('log/error.txt');
    }

    public function testDeleteFile()
    {
        $this->assertFileExists('a.txt');
        $result = $this->taskFilesystemStack()
            ->stopOnFail()
            ->remove('a.txt')
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileDoesNotExist('a.txt');
    }

    public function testCrossVolumeRename()
    {
        $fsStack = $this->taskFilesystemStack()
            ->mkdir('log')
            ->touch('log/error.txt');
        $result = $fsStack->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());

        // We can't force _rename to run the cross-volume
        // code path, so we will directly call the protected
        // method crossVolumeRename to test to ensure it works.
        // We will get a reference to it via reflection, set
        // the reflected method object to public, and then
        // call it via reflection.
        $class = new \ReflectionClass('\Robo\Task\Filesystem\FilesystemStack');
        $method = $class->getMethod('crossVolumeRename');
        $method->setAccessible(true);
        $actualFsStackTask = $fsStack->getCollectionBuilderCurrentTask();
        $method->invokeArgs($actualFsStackTask, ['log', 'logfiles']);

        $this->assertFileDoesNotExist('log/error.txt');
        $this->assertFileExists('logfiles/error.txt');
    }

}
