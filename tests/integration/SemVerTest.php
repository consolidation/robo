<?php
namespace Robo;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use Robo\Traits\TestTasksTrait;

class SemVerTest extends TestCase
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

    public function testSemVerFileWrite()
    {
        $this->fixtures->createAndCdToSandbox();

        $sampleCss = $this->fixtures->dataFile('sample.css');

        $outputFile = '.semver';

        $result = $this->taskSemVer($outputFile)
            ->increment()
            ->run();

        $this->assertTrue($result->wasSuccessful(), $result->getMessage());

        $this->assertFileExists($outputFile);
        $outputFileContents = file_get_contents($outputFile);
        $this->assertStringContainsString('major', $outputFileContents, 'Semver file has expected structure');
    }
}
