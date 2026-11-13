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

    /**
     * @dataProvider semVerFileMetadataProvider
     */
    public function testSemVerFileReadMetadata($metadata, $expected)
    {
        $this->fixtures->createAndCdToSandbox();

        $semverFile = '.semver';
        file_put_contents(
            $semverFile,
            sprintf(Task\Development\SemVer::SEMVER, 1, 2, 3, '', $metadata)
        );

        $result = $this->taskSemVer($semverFile)
            ->run();

        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertEquals($expected, $result->getMessage());
    }

    public static function semVerFileMetadataProvider()
    {
        return [
            'empty metadata' => ['', 'v1.2.3'],
            'hyphenated, dot-separated metadata' => ['build-7.x', 'v1.2.3+build-7.x'],
        ];
    }
}
