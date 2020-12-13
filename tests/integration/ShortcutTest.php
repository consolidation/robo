<?php
namespace Robo;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use Robo\Traits\TestTasksTrait;

class ShortcutTest extends TestCase
{
    use TestTasksTrait;
    use Task\Filesystem\Tasks;
    use Task\Filesystem\Shortcuts;
    use Task\Logfile\Tasks;
    use Task\Logfile\Shortcuts;

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

    public function testCopyDirShortcut()
    {
        // copy dir with _copyDir shortcut
        $result = $this->_copyDir('box', 'bin');
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('bin');
        $this->assertFileExists('bin/robo.txt');
    }

    public function testMirrorDirShortcut()
    {
        // mirror dir with _mirrorDir shortcut
        $result = $this->_mirrorDir('box', 'bin');
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('bin');
        $this->assertFileExists('bin/robo.txt');
    }

    public function testTruncateLogShortcut()
    {
        $result = $this->_truncateLog(['box/robo.txt']);
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('box/robo.txt');
    }

    public function testRotateLogShortcut()
    {
        $result = $this->_rotateLog(['box/robo.txt']);
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('box/robo.txt');
    }
}
