<?php
namespace Robo;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use Robo\Traits\TestTasksTrait;

class CleanDirTest extends TestCase
{
    use TestTasksTrait;
    use Task\Filesystem\Tasks;

    protected $fixtures;

    public function setUp(): void
    {
        $this->fixtures = new Fixtures();
        $this->initTestTasksTrait();
    }

    public function tearDown(): void
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
        $this->assertFileDoesNotExist('box/robo.txt');
        $this->assertFileExists('a.txt');
    }

}
