<?php

namespace Robo;

use Robo\Traits\TestTasksTrait;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Command\Command;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Commands registered on an application that is not a \Robo\Application.
 *
 * Symfony Console < 7.4 has no Application::addCommand(), and only
 * \Robo\Application provides it there, so these tests fail on Symfony 6.4
 * if registration calls addCommand() unconditionally.
 */
class RegisterCommandsTest extends TestCase
{
    use TestTasksTrait;
    use Task\Base\Tasks;

    public function setUp(): void
    {
        $this->initTestTasksTrait();
    }

    public function testRegisterCommandFileOnSymfonyApplication()
    {
        $app = new SymfonyApplication();

        Robo::register($app, RoboFileFixture::class);

        $this->assertTrue($app->has('test:array-args'));
    }

    public function testRegisterCommandInstanceOnSymfonyApplication()
    {
        $app = new SymfonyApplication();

        Robo::register($app, new Command('fixture:command'));

        $this->assertTrue($app->has('fixture:command'));
    }

    public function testRuntimeRunnerRegisterCommandClassOnSymfonyApplication()
    {
        $app = new SymfonyApplication();
        $runner = new Runtime\Runner();

        $runner->registerCommandClass($app, RoboFileFixture::class);

        $this->assertTrue($app->has('test:array-args'));
    }

    public function testAddCommandToApplicationFallsBackToAdd()
    {
        $app = new class () {
            public $commands = [];

            public function add(Command $command): ?Command
            {
                $this->commands[] = $command;
                return $command;
            }
        };
        $command = new Command('fixture:command');

        $result = Robo::addCommandToApplication($app, $command);

        $this->assertSame([$command], $app->commands);
        $this->assertSame($command, $result);
    }
}
