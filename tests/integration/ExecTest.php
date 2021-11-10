<?php
namespace Robo;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use Robo\Traits\TestTasksTrait;

class ExecTest extends TestCase
{
    use TestTasksTrait;
    use Task\Base\Tasks;

    public function setUp(): void
    {
        $this->initTestTasksTrait();
    }

    public function testExecLsCommand()
    {
        $command = strncasecmp(PHP_OS, 'WIN', 3) == 0 ? 'dir' : 'ls';
        $res = $this->taskExec($command)->interactive(false)->run();
        $this->assertTrue($res->wasSuccessful());
        $this->assertStringContainsString(
            'src',
            $res->getMessage());
        $this->assertStringContainsString(
            'codeception.yml',
            $res->getMessage());
    }

    public function testMultipleEnvVars()
    {
        $task = $this->taskExec('env')->interactive(false);
        $task->env('FOO', 'BAR');
        $task->env('BAR', 'BAZ');
        $result = $task->run();
        $this->assertTrue($result->wasSuccessful(), $result->getMessage());
        // Verify that the text contains our environment variable.
        $this->assertStringContainsString(
            'FOO=BAR',
            $result->getMessage());
        $this->assertStringContainsString(
            'BAR=BAZ',
            $result->getMessage());

        // Now verify that we can reset a value that was previously set.
        $task = $this->taskExec('env')->interactive(false);
        $task->env('FOO', 'BAR');
        $task->env('FOO', 'BAZ');
        $result = $task->run();
        $this->assertTrue($result->wasSuccessful());
        // Verify that the text contains the most recent environment variable.
        $this->assertStringContainsString(
            'FOO=BAZ',
            $result->getMessage());
    }
}
