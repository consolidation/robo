<?php
namespace Robo;

use PHPUnit\Framework\TestCase;
use Robo\Traits\TestTasksTrait;

class DeleteDirTest extends TestCase
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

    public function testDeleteDir()
    {
        $this->fixtures->createAndCdToSandbox();

        $this->assertFileExists('box');
        $this->assertFileExists('box/robo.txt');
        $result = $this->taskDeleteDir(['box'])
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileNotExists('box');
        $this->assertFileNotExists('box/robo.txt');
    }

}
