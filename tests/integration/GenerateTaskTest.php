<?php
namespace Robo;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use Robo\Traits\TestTasksTrait;

class GenerateTaskTest extends TestCase
{
    use TestTasksTrait;
    use Collection\Tasks;
    use Task\Development\Tasks;

    public function setUp(): void
    {
        $this->initTestTasksTrait();
    }

    public function testTaskGeneration()
    {
        $result = $this->taskGenTask('Symfony\Component\Filesystem\Filesystem', 'FilesystemStack')->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertStringContainsString(
          'protected function _chgrp($files, $group, $recursive = false)',
          $result->getMessage());
    }

}
