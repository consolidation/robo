<?php
namespace Robo;

use PHPUnit\Framework\TestCase;
use Robo\Traits\TestTasksTrait;

class CopyDirTest extends TestCase
{
    use TestTasksTrait;
    use Task\Filesystem\loadTasks;

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

    public function testCopyDir()
    {
        $this->fixtures->createAndCdToSandbox();

        $result = $this->taskCopyDir(['box' => 'bin'])
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertFileExists('bin');
        $this->assertFileExists('bin/robo.txt');
    }

    /**
     * Data provider for overwrite test
     */
    public function copyDirWithOverwriteData()
    {
        return [
            [
                true,
                'some existing file',
            ],
            [
                false,
                'newer data',
            ],
        ];
    }

    /**
     * @dataProvider copyDirWithOverwriteData
     */
    public function testCopyDirWithOverwrite($overwriteValue, $expected)
    {
        $this->fixtures->createAndCdToSandbox();

        @mkdir('some_destination');
        @mkdir('some_destination/deeply');
        file_put_contents('some_destination/deeply/existing_file', 'newer data');

        $this->assertFileExists('some');
        $this->assertFileExists('some/deeply/existing_file');
        $result = $this->taskCopyDir(['some' => 'some_destination'])
            ->overwrite($overwriteValue)
            ->run();
        $this->assertTrue($result->wasSuccessful());

        $this->assertFileExists('some_destination');
        $this->assertFileExists('some_destination/deeply/existing_file');

        $actual = trim(file_get_contents('some_destination/deeply/existing_file'));
        $this->assertEquals($expected, $actual);
    }

    public function testCopyRecursive()
    {
        $this->fixtures->createAndCdToSandbox();

        $this->assertFileExists('some/deeply/nested');
        $this->assertFileExists('some/deeply/nested/structu.re');
        $result = $this->taskCopyDir(['some/deeply' => 'some_destination/deeply'])
            ->run();
        $this->assertTrue($result->wasSuccessful());
        $this->assertFileExists('some_destination/deeply/nested');
        $this->assertFileExists('some_destination/deeply/nested/structu.re');

    }

    public function testCopyRecursiveWithExcludedFile()
    {
        $this->fixtures->createAndCdToSandbox();

        $this->assertFileExists('some/deeply/nested');
        $this->assertFileExists('some/deeply/nested2');
        $this->assertFileExists('some/deeply/nested3');
        $this->assertFileExists('some/deeply/nested3/nested31');
        $this->assertFileExists('some/deeply/nested4');
        $this->assertFileExists('some/deeply/nested4/nested41');
        $this->assertFileExists('some/deeply/nested/structu.re');
        $this->assertFileExists('some/deeply/nested/structu1.re');
        $this->assertFileExists('some/deeply/nested/structu2.re');
        $this->assertFileExists('some/deeply/nested/structu3.re');
        $this->assertFileExists('some/deeply/nested2/structu21.re');
        $this->assertFileExists('some/deeply/nested3/structu31.re');
        $this->assertFileExists('some/deeply/nested3/structu32.re');
        $this->assertFileExists('some/deeply/nested3/nested31/structu311.re');
        $this->assertFileExists('some/deeply/nested4/nested41/structu411.re');
        $this->assertFileExists('some/deeply/nested4/nested41/structu412.re');

        $result = $this->taskCopyDir(['some/deeply' => 'some_destination/deeply'])
            ->exclude([
                // Basename exclusion.
                'structu1.re',
                // File in subdir exclusion.
                'some/deeply/nested/structu3.re',
                // Dir exclusion.
                'nested2',
                // Subdir exclusion.
                'some/deeply/nested3/nested31',
                // Subpath within source exclusion.
                'nested3/structu31.re',
                // File in deeper subpath within source exclusion.
                'nested4/nested41/structu411.re',
            ])
            ->run();
        $this->assertTrue($result->wasSuccessful());

        $this->assertFileExists('some_destination/deeply/nested');
        $this->assertFileNotExists('some_destination/deeply/nested2');
        $this->assertFileExists('some_destination/deeply/nested3');
        $this->assertFileNotExists('some_destination/deeply/nested3/nested31');
        $this->assertFileExists('some_destination/deeply/nested4');
        $this->assertFileExists('some_destination/deeply/nested4/nested41');
        $this->assertFileExists('some_destination/deeply/nested/structu.re');
        $this->assertFileNotExists('some_destination/deeply/nested/structu1.re');
        $this->assertFileExists('some_destination/deeply/nested/structu2.re');
        $this->assertFileNotExists('some_destination/deeply/nested/structu3.re');
        $this->assertFileNotExists('some_destination/deeply/nested2/structu21.re');
        $this->assertFileNotExists('some_destination/deeply/nested3/structu31.re');
        $this->assertFileExists('some_destination/deeply/nested3/structu32.re');
        $this->assertFileNotExists('some_destination/deeply/nested3/nested31/structu311.re');
        $this->assertFileNotExists('some_destination/deeply/nested4/nested41/structu411.re');
        $this->assertFileExists('some_destination/deeply/nested4/nested41/structu412.re');
    }
}
