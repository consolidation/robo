<?php
namespace Robo;

use PHPUnit\Framework\TestCase;
use Robo\Traits\TestTasksTrait;

class CleanDirTest extends TestCase
{
    use TestTasksTrait;
    use Task\Filesystem\loadTasks;

    protected $fixtures;

    public function setUp()
    {
        $this->fixtures = new Fixtures();
        $this->initTestTasksTrait();
    }

    public function tearDown()
    {
        $this->fixtures->cleanup();
    }

    public function testCleanDir()
    {
        $this->fixtures->createAndCdToSandbox();

        $this->assertFileExists('box/robo.txt');
        $result = $this->taskCleanDir(['box'])
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileNotExists('box/robo.txt');
        $this->assertFileExists('a.txt');
    }

}
