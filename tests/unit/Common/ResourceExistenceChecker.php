<?php
use Robo\Common\ResourceExistenceChecker;

class ResourceExistenceCheckerTest extends \Codeception\TestCase\Test
{
    use ResourceExistenceChecker;

    protected $testDir = null;

    protected $testFile = null;

    protected function _before()
    {
        $this->apigen = test::double('Robo\Task\ApiGen\ApiGen', [
            'executeCommand' => null,
            'output' => new \Symfony\Component\Console\Output\NullOutput()
        ]);
        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }
        $this->testDir = __DIR__ . '..' . DS . '..' . DS . 'data' . DS;
        $this->testFile = $this->testDir . 'dump.sql';
    }

    /**
     * testCheckResources
     */
    public function testCheckResources()
    {
        $this->assertTrue($this->checkResources($this->testDir, 'dir'));
        $this->assertTrue($this->checkResources([
            $this->testDir,
            $this->testFile
        ]));
    }

    /**
     * @expectException \InvalidArgumentException
     */
    public function testCheckResourcesException()
    {
        $this->checkResources('does not exist', 'invalid type');
    }

    /**
     * testCheckResource
     */
    public function testCheckResource()
    {
        $this->assertTrue($this->checkResource($this->testDir, 'dir'));
        $this->assertTrue($this->checkResource($this->testDir, 'fileAndDir'));
        $this->assertTrue($this->checkResource($this->testFile, 'file'));
        $this->assertTrue($this->checkResource($this->testFile, 'fileAndDir'));

        $this->assertFalse($this->checkResource('does-not-exist', 'dir'));
        $this->assertFalse($this->checkResource('does-not-exist', 'fileAndDir'));
        $this->assertFalse($this->checkResource('does-not-exist', 'file'));
        $this->assertFalse($this->checkResource('does-not-exist', 'fileAndDir'));
    }

    /**
     * testIsDir
     */
    public function testIsDir()
    {
        $this->assertTrue($this->isDir($this->testDir));
        $this->assertFalse($this->isDir('does-not-exist'));
    }

    /**
     * testIsFile
     */
    public function testIsFile()
    {
        $this->assertTrue($this->isFile($this->testFile));
        $this->assertFalse($this->isFile($this->testDir . 'does-not-exist'));
    }
}
