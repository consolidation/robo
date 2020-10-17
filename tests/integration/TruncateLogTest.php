<?php

namespace Robo;

use PHPUnit\Framework\TestCase;
use Robo\Traits\TestTasksTrait;

class TruncateLogTest extends TestCase
{
    use TestTasksTrait;
    use Task\Logfile\Tasks;

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

    public function testTruncateLog()
    {
        $this->fixtures->createAndCdToSandbox();

        $this->assertStringEqualsFile('box/robo.txt', 'HELLOROBO');
        $result = $this->taskTruncateLog('box/robo.txt')
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('box/robo.txt');
        $this->assertStringEqualsFile('box/robo.txt', '');
    }

    public function testTruncateMultipleLogs()
    {
        $this->fixtures->createAndCdToSandbox();

        $this->assertStringEqualsFile('a.txt', 'A');
        $this->assertStringEqualsFile('b.txt', 'B');
        $result = $this->taskTruncateLog(['a.txt', 'b.txt'])
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('a.txt');
        $this->assertFileExists('b.txt');
        $this->assertStringEqualsFile('a.txt', '');
        $this->assertStringEqualsFile('b.txt', '');
    }

    public function testTruncateOrCreateLog()
    {
        $this->fixtures->createAndCdToSandbox();

        $this->assertFileNotExists('box/new.log');
        $result = $this->taskTruncateLog(['box/new.log'])
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('box/new.log');
        $this->assertStringEqualsFile('box/new.log', '');
    }

    public function testTruncateLogFileMode()
    {
        $this->fixtures->createAndCdToSandbox();

        $this->assertFileExists('box/robo.txt');
        $result = $this->taskTruncateLog(['box/robo.txt'])
            ->chmod(0777)
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('box/robo.txt');
        $this->assertFileIsReadable('box/robo.txt');
        $this->assertFileIsWritable('box/robo.txt');
        $mode = substr(sprintf('%o', fileperms('box/robo.txt')), -4);
        $this->assertSame($mode, '0777');
        $this->assertEquals($mode, '0777');
    }
}
