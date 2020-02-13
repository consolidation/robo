<?php
namespace Robo;

use PHPUnit\Framework\TestCase;
use Robo\Traits\TestTasksTrait;

class FlattenDirTest extends TestCase
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
