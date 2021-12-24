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
        $this->fixtures->createAndCdToSandbox();

        $pharFile = 'test.phar';
        $this->taskPackPhar($pharFile)
            ->addStripped(
                'annotated.php',
                $this->fixtures->dataFile('TestedRoboFile.php'))
            ->run();

        $phar = new \Phar($pharFile);

        $fileContent = $phar['annotated.php']->getContent();
        $this->assertStringContainsString('#[\ReturnTypeWillChange]', $fileContent, 'Annotation was stripped');
    }
}
