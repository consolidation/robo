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
        list($tryInputOutput, $statusCode) = $this->executeCommand('try:input', ["I'm great!", "yes", "PHP", "1234"]);
        $this->assertEquals(0, $statusCode);
        $this->assertContains("I'm great!", $tryInputOutput);
        $this->assertContains("PHP", $tryInputOutput);
        $this->assertContains("1234", $tryInputOutput);
    }
}
