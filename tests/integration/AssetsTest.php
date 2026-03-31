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
        $this->assertStringContainsString('body', $outputCssContents, 'Minified file has some content from the source file');
        $this->assertStringNotContainsString('Sample css file', $outputCssContents, 'Minified file does not contain comment from source file');
    }

    public function testImageMinification()
    {
        if (version_compare(PHP_VERSION, '8.1') >= 0) {
            $this->markTestSkipped('Not compatible with PHP 8.1 yet');
        }

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

    public function testScssCompilationWithScssPhpCompiler()
    {
        if (! class_exists('\ScssPhp\ScssPhp\Compiler')) {
            $this->markTestSkipped('scssphp/scssphp is not installed.');
        }

        $this->fixtures->createAndCdToSandbox();
        mkdir('scss');

        file_put_contents('scss/_variables.scss', '$primary: #fff;');
        file_put_contents('scss/input.scss', <<<'SCSS'
@import 'variables';

body {
  color: $primary;
}
SCSS
        );

        $result = $this->taskScss([
            'scss/input.scss' => 'output.css',
        ])
            ->importDir('scss')
            ->setFormatter('compressed')
            ->run();

        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('output.css');
        $this->assertSame('body{color:#fff}', trim(file_get_contents('output.css')));
    }

    public function testScssCompilationWithBugoCompiler()
    {
        if (! class_exists('\Bugo\SCSS\Compiler')) {
            $this->markTestSkipped('bugo/scss-php is not installed.');
        }

        $this->fixtures->createAndCdToSandbox();
        mkdir('scss');

        file_put_contents('scss/_variables.scss', '$primary: #fff;');
        file_put_contents('scss/input.scss', <<<'SCSS'
@import 'variables';

body {
  color: $primary;
}
SCSS
        );

        $result = $this->taskScss([
            'scss/input.scss' => 'output.css',
        ])
            ->importDir('scss')
            ->compiler('scss')
            ->setFormatter('compressed')
            ->run();

        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('output.css');
        $this->assertSame('body{color:#fff}', trim(file_get_contents('output.css')));
    }
}
