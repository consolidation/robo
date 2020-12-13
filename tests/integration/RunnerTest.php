<?php
namespace Robo;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use Robo\Traits\TestTasksTrait;

class RunnerTest extends TestCase
{
    use TestTasksTrait;
    use Task\Base\Tasks;

    /**
     * @var \Robo\Runner
     */
    private $runner;

    public function setUp(): void
    {
        $container = $this->initTestTasksTrait();
        $this->runner = new \Robo\Runner('\Robo\RoboFileFixture');
        $this->runner->setContainer($container);
    }

    public function testThrowsExceptionWhenNoContainerAvailable()
    {
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessageMatches(
            '/container is not initialized yet.*/'
        );
        Robo::unsetContainer();
        Robo::getContainer();
    }

    public function testRunnerNoSuchCommand()
    {
        $argv = ['placeholder', 'no-such-command'];
        $this->runner->execute($argv, null, null, $this->capturedOutputStream());
        $this->assertOutputContains('Command "no-such-command" is not defined.');
    }

    public function testRunnerList()
    {
        $argv = ['placeholder', 'list'];
        $this->runner->execute($argv, null, null, $this->capturedOutputStream());
        $this->assertOutputContains('test:array-args');
    }

    public function testRunnerTryArgs()
    {
        $argv = ['placeholder', 'test:array-args', 'a', 'b', 'c'];
        $this->runner->execute($argv, null, null, $this->capturedOutputStream());

        $expected = <<<EOT
The parameters passed are:
array (
  0 => 'a',
  1 => 'b',
  2 => 'c',
)
EOT;
        $this->assertOutputEquals($expected);
    }

    public function testSymfonyStyle()
    {
        $argv = ['placeholder', 'test:symfony-style'];
        $this->runner->execute($argv, null, null, $this->capturedOutputStream());
        $this->assertOutputContains('Some text in section one.');
    }

    public function testStyleInjector()
    {
        $argv = ['placeholder', 'test:style-injector'];
        $this->runner->execute($argv, null, null, $this->capturedOutputStream());
        $this->assertOutputContains('Some text in section one printed via injected io object.');
    }

    public function testSymfony()
    {
        $argv = ['placeholder', 'test:symfony', 'a', 'b', 'c', '--foo=bar', '--foo=baz', '--foo=boz'];
        $this->runner->execute($argv, null, null, $this->capturedOutputStream());
        $expected = <<<EOT
The parameters passed are:
array (
  0 => 'a',
  1 => 'b',
  2 => 'c',
)
The options passed via --foo are:
array (
  0 => 'bar',
  1 => 'baz',
  2 => 'boz',
)
EOT;
        $this->assertOutputEquals($expected);
    }

    public function testCommandEventHook()
    {
        $argv = ['placeholder', 'test:command-event'];
        $this->runner->execute($argv, null, null, $this->capturedOutputStream());

        $expected = <<<EOT
This is the command-event hook for the test:command-event command.
 This is the main method for the test:command-event command.
 This is the post-command hook for the test:command-event command.
EOT;
        $this->assertOutputContains($expected);
    }

    public function testCustomEventHook()
    {
        $argv = ['placeholder', 'test:custom-event'];
        $this->runner->execute($argv, null, null, $this->capturedOutputStream());

        $expected = 'one,two';
        $this->assertOutputContains($expected);
    }

    public function testRoboStaticRunMethod()
    {
        $argv = ['placeholder', 'test:symfony-style'];
        $commandFiles = ['\Robo\RoboFileFixture'];
        Robo::run($argv, $commandFiles, 'MyApp', '1.2.3', $this->capturedOutputStream());
        $this->assertOutputContains('Some text in section one.');
    }

    public function testDeploy()
    {
        $argv = ['placeholder', 'test:deploy', '--simulate'];
        $this->runner->execute($argv, null, null, $this->capturedOutputStream());
        $this->assertOutputContains('[Simulator] Simulating Remote\\Ssh(\'mysite.com\', null)');
        $this->assertOutputContains('[Simulator] Running ssh mysite.com \'cd "/var/www/somesite" && git pull\'');
    }

    public function testRunnerTryError()
    {
        $argv = ['placeholder', 'test:error'];
        $result = $this->runner->execute($argv, null, null, $this->capturedOutputStream());

        $this->assertOutputContains('[Exec] Running ls xyzzy');
        $this->assertTrue($result > 0);
    }

    public function testRunnerTrySimulatedError()
    {
        $argv = ['placeholder', 'test:error', '--simulate'];
        $result = $this->runner->execute($argv, null, null, $this->capturedOutputStream());

        $this->assertOutputContains('Simulating Exec');
        $this->assertEquals(0, $result);
    }

    public function testRunnerTryException()
    {
        $argv = ['placeholder', 'test:exception', '--task'];
        $result = $this->runner->execute($argv, null, null, $this->capturedOutputStream());

        $this->assertOutputContains('Task failed with an exception');
        $this->assertEquals(1, $result);
    }

    public function testInitCommand()
    {
        $container = \Robo\Robo::getContainer();
        $app = $container->get('application');
        $app->addInitRoboFileCommand(getcwd() . '/testRoboFile.php', 'RoboTestClass');

        $argv = ['placeholder', 'init'];
        $status = $this->runner->run($argv, $this->capturedOutputStream(), $app);
        $this->assertOutputContains('testRoboFile.php will be created in the current directory');
        $this->assertEquals(0, $status);

        $this->assertTrue(file_exists('testRoboFile.php'));
        $commandContents = file_get_contents('testRoboFile.php');
        unlink('testRoboFile.php');
        $this->assertStringContainsString('class RoboTestClass', $commandContents);
    }

    public function testTasksStopOnFail()
    {
        $argv = ['placeholder', 'test:stop-on-fail'];
        $result = $this->runner->execute($argv, null, null, $this->capturedOutputStream());

        $this->assertOutputContains('[');
        $this->assertTrue($result > 0);
    }

    public function testInvalidRoboDirectory()
    {
        $runnerWithNoRoboFile = new \Robo\Runner();

        $argv = ['placeholder', 'no-such-command', '-f', 'no-such-directory'];
        $result = $runnerWithNoRoboFile->execute($argv, null, null, $this->capturedOutputStream());

        $this->assertOutputContains('Path `no-such-directory` is invalid; please provide a valid absolute path to the Robofile to load.');
    }

    public function testUnloadableRoboFile()
    {
        $runnerWithNoRoboFile = new \Robo\Runner();

        $argv = ['placeholder', 'help', 'test:custom-event', '-f', dirname(__DIR__) . '/src/RoboFileFixture.php'];
        $result = $runnerWithNoRoboFile->execute($argv, null, null, $this->capturedOutputStream());

        // We cannot load RoboFileFixture.php via -f / --load-from because
        // it has a namespace, and --load-from does not support that.
        $this->assertOutputContains('Class RoboFileFixture was not loaded');
    }

    public function testRunnerQuietOutput()
    {
        $argv = ['placeholder', 'test:verbosity', '--quiet'];
        $result = $this->runner->execute($argv, null, null, $this->capturedOutputStream());

        $this->assertOutputNotContains('This command will print more information at higher verbosity levels');
        $this->assertOutputNotContains('This is a verbose message (-v).');
        $this->assertOutputNotContains('This is a very verbose message (-vv).');
        $this->assertOutputNotContains('This is a debug message (-vvv).');
        $this->assertOutputNotContains(' [warning] This is a warning log message.');
        $this->assertOutputNotContains(' [notice] This is a notice log message.');
        $this->assertOutputNotContains(' [debug] This is a debug log message.');
        $this->assertEquals(0, $result);
    }

    public function testRunnerVerboseOutput()
    {
        $argv = ['placeholder', 'test:verbosity', '-v'];
        $result = $this->runner->execute($argv, null, null, $this->capturedOutputStream());

        $this->assertOutputContains('This command will print more information at higher verbosity levels');
        $this->assertOutputContains('This is a verbose message (-v).');
        $this->assertOutputNotContains('This is a very verbose message (-vv).');
        $this->assertOutputNotContains('This is a debug message (-vvv).');
        $this->assertOutputContains(' [warning] This is a warning log message.');
        $this->assertOutputContains(' [notice] This is a notice log message.');
        $this->assertOutputNotContains(' [debug] This is a debug log message.');
        $this->assertEquals(0, $result);
    }

    public function testRunnerVeryVerboseOutput()
    {
        $argv = ['placeholder', 'test:verbosity', '-vv'];
        $result = $this->runner->execute($argv, null, null, $this->capturedOutputStream());

        $this->assertOutputContains('This command will print more information at higher verbosity levels');
        $this->assertOutputContains('This is a verbose message (-v).');
        $this->assertOutputContains('This is a very verbose message (-vv).');
        $this->assertOutputNotContains('This is a debug message (-vvv).');
        $this->assertOutputContains(' [warning] This is a warning log message.');
        $this->assertOutputContains(' [notice] This is a notice log message.');
        $this->assertOutputNotContains(' [debug] This is a debug log message.');
        $this->assertEquals(0, $result);
    }

    public function testRunnerVerbosityThresholdVerbose()
    {
        $argv = ['placeholder', 'test:verbosity-threshold', '-v'];
        $result = $this->runner->execute($argv, null, null, $this->capturedOutputStream());

        $this->assertOutputContains('This command will print more information at higher verbosity levels');
        $this->assertOutputContains("Running echo verbose or higher\nverbose or higher");
        $this->assertOutputNotContains('very verbose or higher');
        $this->assertEquals(0, $result);
    }

    public function testRunnerVerbosityThresholdCompatabilityVerbose()
    {
        $argv = ['placeholder', 'test:verbosity-threshold-compatability', '-v'];
        $result = $this->runner->execute($argv, null, null, $this->capturedOutputStream());

        $this->assertOutputContains('This command will print more information at higher verbosity levels');
        $this->assertOutputContains("Running echo verbose or higher\nverbose or higher");
        $this->assertOutputNotContains('very verbose or higher');
        $this->assertEquals(0, $result);
    }

    public function testRunnerVerbosityThresholdVeryVerbose()
    {
        $argv = ['placeholder', 'test:verbosity-threshold', '-vv'];
        $result = $this->runner->execute($argv, null, null, $this->capturedOutputStream());

        $this->assertOutputContains('This command will print more information at higher verbosity levels');
        $this->assertOutputContains("Running echo verbose or higher\nverbose or higher");
        $this->assertOutputContains("Running echo very verbose or higher\nvery verbose or higher");
        $this->assertEquals(0, $result);
    }

    public function testRunnerDebugOutput()
    {
        $argv = ['placeholder', 'test:verbosity', '-vvv'];
        $result = $this->runner->execute($argv, null, null, $this->capturedOutputStream());

        $this->assertOutputContains('This command will print more information at higher verbosity levels');
        $this->assertOutputContains('This is a verbose message (-v).');
        $this->assertOutputContains('This is a very verbose message (-vv).');
        $this->assertOutputContains('This is a debug message (-vvv).');
        $this->assertOutputContains(' [warning] This is a warning log message.');
        $this->assertOutputContains(' [notice] This is a notice log message.');
        $this->assertOutputContains(' [debug] This is a debug log message.');
        $this->assertEquals(0, $result);
    }
}
