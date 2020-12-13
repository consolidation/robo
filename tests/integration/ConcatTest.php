<?php
namespace Robo;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use Robo\Traits\TestTasksTrait;

class ConcatTest extends TestCase
{
    use TestTasksTrait;
    use Task\File\Tasks;

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

    public function testConcat()
    {
        $this->fixtures->createAndCdToSandbox();

        $result = $this->taskConcat(['a.txt', 'b.txt'])
            ->to('merged.txt')
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('merged.txt');
        $expected = "A\nB\n";
        $actual = file_get_contents('merged.txt');
        $this->assertEquals($expected, $actual);


    }

}
