<?php

namespace Robo;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use Robo\Traits\TestTasksTrait;

class PackPharTest extends TestCase
{
    use TestTasksTrait;
    use Task\Development\Tasks;

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

    public function testAddStrippedFileContainingAnnotation()
    {
        if (ini_get('phar.readonly') === '1') {
            $this->markTestSkipped('phar.readonly = 1');
        }

        $this->fixtures->createAndCdToSandbox();

        $pharFile = 'test.phar';
        $this->taskPackPhar($pharFile)
            ->addStripped(
                'annotated.php',
                $this->fixtures->dataFile('TestedRoboFile.php')
            )
            ->run();

        $phar = new \Phar($pharFile);

        $fileContent = $phar['annotated.php']->getContent();
        $this->assertStringContainsString('#[\ReturnTypeWillChange]', $fileContent, 'Annotation was stripped');
    }

    public function testCompressMoreThan1000Files()
    {
        if (ini_get('phar.readonly') === '1') {
            $this->markTestSkipped('phar.readonly = 1');
        }
        if (!in_array('GZ', \Phar::getSupportedCompression())) {
            $this->markTestSkipped('GZ compression is not supported');
        }

        $this->fixtures->createAndCdToSandbox();

        $pharFile = 'compressed.phar';
        $task = $this->taskPackPhar($pharFile)->compress();
        for ($i = 0; $i < 1100; $i++) {
            $task->addFile("file$i.php", $this->fixtures->dataFile('TestedRoboFile.php'));
        }
        $task->run();

        $phar = new \Phar($pharFile);

        $this->assertCount(1100, $phar);
        $this->assertTrue($phar['file0.php']->isCompressed(\Phar::GZ), 'First file is not compressed');
        $this->assertTrue($phar['file1099.php']->isCompressed(\Phar::GZ), 'Last file is not compressed');
    }
}
