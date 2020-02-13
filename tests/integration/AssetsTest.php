<?php
namespace Robo;

use PHPUnit\Framework\TestCase;
use Robo\Traits\TestTasksTrait;

class AssetsTest extends TestCase
{
    use TestTasksTrait;
    use Task\Assets\loadTasks;

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

    public function testCssMinification()
    {
        if (version_compare(PHP_VERSION, '7.2.0') >= 0) {
          $this->markTestSkipped('natxet/cssmin uses deprecated "each()" function (PHP 7.2+)');
        }

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
				$this->assertContains('body', $outputCssContents, 'Minified file has some content from the source file');
				$this->assertNotContains('Sample css file', $outputCssContents, 'Minified file does not contain comment from source file');
    }

}
