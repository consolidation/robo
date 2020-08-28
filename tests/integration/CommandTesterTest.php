<?php
namespace Robo;

use PHPUnit\Framework\TestCase;
use Robo\Traits\CommandTesterTrait;
use RoboExample\Robo\Plugin\Commands\ExampleCommands;

class CommandTestertTest extends TestCase
{
    use CommandTesterTrait;

    public function setUp()
    {
        $this->setupCommandTester(ExampleCommands::class);
    }

    public function testInputApis()
    {
        list($tryInputOutput, $statusCode) = $this->executeCommand('try:default-input', [], [], null, false);
        $this->assertEquals(0, $statusCode);
        $this->assertContains("super", $tryInputOutput);
        $this->assertContains("PHP", $tryInputOutput);
    }

    public function testTesterWithOptions()
    {
        list($execOutput, $statusCode) = $this->executeCommand('try:exec', []);
        $this->assertEquals(0, $statusCode);
        list($execOutput, $statusCode) = $this->executeCommand('try:exec', [], ['--error']);
        $this->assertNotEquals(0, $statusCode);
    }
}
