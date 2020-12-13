<?php

namespace Robo;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use Robo\Traits\TestTasksTrait;

class RotateLogTest extends TestCase
{
    use TestTasksTrait;
    use Task\Logfile\Tasks;

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

    public function testRotateLog()
    {
        $this->fixtures->createAndCdToSandbox();

        $this->assertStringEqualsFile('box/robo.txt', 'HELLOROBO');
        $result = $this->taskRotateLog('box/robo.txt')
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('box/robo.txt');
        $this->assertFileExists('box/robo.txt.1');
        $this->assertStringEqualsFile('box/robo.txt', '');
        $this->assertStringEqualsFile('box/robo.txt.1', 'HELLOROBO');
    }

    public function testRotateMultipleLogs()
    {
        $this->fixtures->createAndCdToSandbox();

        $this->assertStringEqualsFile('box/robo.txt', 'HELLOROBO');
        copy('box/robo.txt', 'box/first.log');
        copy('box/robo.txt', 'box/second.log');
        $this->assertStringEqualsFile('box/first.log', 'HELLOROBO');
        $this->assertStringEqualsFile('box/second.log', 'HELLOROBO');
        $result = $this->taskRotateLog(['box/first.log', 'box/second.log'])
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('box/robo.txt');
        $this->assertFileDoesNotExist('box/robo.txt.1');
        $this->assertFileExists('box/first.log');
        $this->assertFileExists('box/first.log.1');
        $this->assertFileExists('box/second.log');
        $this->assertFileExists('box/second.log.1');
        $this->assertStringEqualsFile('box/robo.txt', 'HELLOROBO');
        $this->assertStringEqualsFile('box/first.log', '');
        $this->assertStringEqualsFile('box/first.log.1', 'HELLOROBO');
        $this->assertStringEqualsFile('box/second.log', '');
        $this->assertStringEqualsFile('box/second.log.1', 'HELLOROBO');
    }

    public function testRotateLogInvalidArgumentException()
    {
        $this->fixtures->createAndCdToSandbox();

        $this->assertStringEqualsFile('box/robo.txt', 'HELLOROBO');
        $this->expectException(\InvalidArgumentException::class);
        $this->taskRotateLog(['box/robo.txt'])
            ->keep(0)
            ->run();
    }

    public function testRotateLogKeep()
    {
        $this->fixtures->createAndCdToSandbox();

        $this->assertStringEqualsFile('box/robo.txt', 'HELLOROBO');
        $result = $this->taskRotateLog(['box/robo.txt'])
            ->keep(1)
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $result = $this->taskRotateLog(['box/robo.txt'])
            ->keep(1)
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $filesystemIterator = new \FilesystemIterator(
            'box',
            \FilesystemIterator::SKIP_DOTS
        );
        $this->assertEquals(2, iterator_count($filesystemIterator));
    }

    public function testRotateLogFileMode()
    {
        $this->fixtures->createAndCdToSandbox();

        $this->assertFileExists('box/robo.txt');
        $result = $this->taskRotateLog(['box/robo.txt'])
            ->chmod(0777)
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('box/robo.txt');
        $this->assertFileIsReadable('box/robo.txt');
        $this->assertFileIsWritable('box/robo.txt');
        $mode = substr(
            sprintf(
                '%o',
                (new \SplFileInfo('box/robo.txt'))->getPerms()
            ),
            -4
        );
        $expectedMode = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? '0666' : '0777';
        $this->assertSame($mode, $expectedMode);
        $this->assertEquals($mode, $expectedMode);
    }
}
