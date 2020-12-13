<?php
namespace Robo;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use Robo\Traits\CommandTesterTrait;
use RoboExample\Robo\Plugin\Commands\ExampleCommands;

class CommandTestertTest extends TestCase
{
    use CommandTesterTrait;

    public function setUp(): void
    {
        $this->setupCommandTester(ExampleCommands::class);
    }

    public function testInputApis()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->markTestSkipped('Hidden input test not working on Windows.');
        }
        list($tryInputOutput, $statusCode) = $this->executeCommand('try:input', ["I'm great!", "yes", "PHP", "1234"]);
        $this->assertStringContainsString("I'm great!", $tryInputOutput);
        $this->assertStringContainsString("PHP", $tryInputOutput);
        $this->assertStringContainsString("1234", $tryInputOutput);
        $this->assertEquals(0, $statusCode);
    }
}
