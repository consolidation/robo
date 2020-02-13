<?php
namespace Robo;

use PHPUnit\Framework\TestCase;
use Robo\Traits\TestTasksTrait;

class GenerateTaskTest extends TestCase
{
    use TestTasksTrait;
    use Collection\loadTasks;
    use Task\Development\loadTasks;

    public function setUp()
    {
        $this->initTestTasksTrait();
    }

    public function testTaskGeneration()
    {
        $result = $this->taskGenTask('Symfony\Component\Filesystem\Filesystem', 'FilesystemStack')->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        $this->assertContains(
          'protected function _chgrp($files, $group, $recursive = false)',
          $result->getMessage());
    }

}
