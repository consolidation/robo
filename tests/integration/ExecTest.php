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
        // The `env` command is not available on Windows, use `set` instead.
        $command = strncasecmp(PHP_OS, 'WIN', 3) == 0 ? 'set' : 'env';
        $task = $this->taskExec($command)->interactive(false);
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
        $task = $this->taskExec($command)->interactive(false);
        $task->env('FOO', 'BAR');
        $task->env('FOO', 'BAZ');
        $result = $task->run();
        $this->assertTrue($result->wasSuccessful());
        // Verify that the text contains the most recent environment variable.
        $this->assertStringContainsString(
            'FOO=BAZ',
            $result->getMessage());
    }

    public function testInheritEnv()
    {
        // The `env` and `wc` commands are not available on Windows.
        // Use `set` and `find` instead.
        $command = strncasecmp(PHP_OS, 'WIN', 3) == 0
            ? 'set | find /c /v ""'
            : 'env | wc -l';

        // With no environment variables set, count how many environment
        // variables are present.
        $task = $this->taskExec($command)->interactive(false);
        $result = $task->run();
        $this->assertTrue($result->wasSuccessful());
        $start_count = (int) $result->getMessage();
        $this->assertGreaterThan(0, $start_count);

        // Verify that we get the same amount of environment variables with
        // another exec call.
        $task = $this->taskExec($command)->interactive(false);
        $result = $task->run();
        $this->assertTrue($result->wasSuccessful());
        $this->assertEquals(
            $start_count,
            (int) $result->getMessage());

        // Now run the same command, but this time add another environment
        // variable, and see if our count increases by one.
        $task = $this->taskExec($command)->interactive(false);
        $task->env('FOO', 'BAR');
        $result = $task->run();
        $this->assertTrue($result->wasSuccessful());
        $this->assertEquals($start_count + 1, (int) $result->getMessage());
    }
}
