<?php
namespace Robo;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use Robo\Traits\TestTasksTrait;

class AssetsTest extends TestCase
{
    use TestTasksTrait;
    use Task\Assets\Tasks;

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

    public function testCssMinification()
    {

        $this->fixtures->createAndCdToSandbox();

        $sampleCss = $this->fixtures->dataFile('sample.css');
        $outputCss = 'minifiedSample.css';

        $initialFileSize = filesize($sampleCss);

        $result = $this->taskMinify($sampleCss)
            ->to('minifiedSample.css')
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());

        $this->assertFileExists($outputCss);
        $minifiedFileSize = filesize($outputCss);
        $outputCssContents = file_get_contents($outputCss);

        $this->assertLessThan($initialFileSize, $minifiedFileSize, 'Minified file is smaller than the source file');
        $this->assertGreaterThan(0, $minifiedFileSize, 'Minified file is not empty');
        $this->assertStringContainsString('body', $outputCssContents, 'Minified file has some content from the source file');
        $this->assertStringNotContainsString('Sample css file', $outputCssContents, 'Minified file does not contain comment from source file');
    }

    public function testImageMinification()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->markTestSkipped('Binary download & execution not working on Windows (#1053).');
        }

        $this->fixtures->createAndCdToSandbox();
        mkdir('dist');

        $sampleImage = $this->fixtures->dataFile('sample.png');
        $outputImage = 'dist/sample.png';

        $initialFileSize = filesize($sampleImage);

        $result = $this->taskImageMinify($sampleImage)
            ->setExecutableDir(realpath('') . '/bin') // use sandbox for bin download
            ->to(realpath('') . '/dist')
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());

        $this->assertFileExists($outputImage);
        $minifiedFileSize = filesize($outputImage);

        $this->assertLessThan($initialFileSize, $minifiedFileSize, 'Minified file is smaller than the source file');
        $this->assertGreaterThan(0, $minifiedFileSize, 'Minified file is not empty');
    }
}
