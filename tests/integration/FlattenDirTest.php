<?php
namespace Robo;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use Robo\Traits\TestTasksTrait;

class FlattenDirTest extends TestCase
{
    use TestTasksTrait;
    use Task\Filesystem\Tasks;

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

    public function testFlattenDir()
    {
        $this->fixtures->createAndCdToSandbox();

        $result = $this->taskFlattenDir([
            'some/deeply/nested/*.re' => 'flattened',
            '*.txt' => 'flattened'
            ])
            ->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());

        $this->assertFileExists('flattened');
        $this->assertFileExists('flattened/structu.re');
        $this->assertFileExists('flattened/a.txt');
        $this->assertFileExists('flattened/b.txt');
    }

    public function testFlattenDirIncludingParents()
    {
        $this->fixtures->createAndCdToSandbox();

        $result = $this->taskFlattenDir('some/deeply/nested/*.re')
            ->includeParents([1,1])
            ->parentDir('some')
            ->to('flattened')
            ->run();

        $this->assertFileExists('flattened/deeply/nested');
        $this->assertFileExists('flattened/deeply/nested/structu.re');

    }
}
