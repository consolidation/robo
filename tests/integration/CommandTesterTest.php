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
        if (getenv('SCENARIO') == 'symfony4' && getenv('DEPENDENCIES') == 'lowest') {
            $this->markTestSkipped('There is a bug with a lower dependency of symfony4 in how it handles tty.');
        }
        list($tryInputOutput, $statusCode) = $this->executeCommand('try:input', ["I'm great!", "yes", "PHP", "1234"]);
        $this->assertEquals(0, $statusCode);
        $this->assertContains("I'm great!", $tryInputOutput);
        $this->assertContains("PHP", $tryInputOutput);
        $this->assertContains("1234", $tryInputOutput);
    }

    public function testTesterWithOptions()
    {
        list($execOutput, $statusCode) = $this->executeCommand('try:exec', []);
        $this->assertEquals(0, $statusCode);
        list($execOutput, $statusCode) = $this->executeCommand('try:exec', [], ['--error']);
        $this->assertNotEquals(0, $statusCode);
    }
}
