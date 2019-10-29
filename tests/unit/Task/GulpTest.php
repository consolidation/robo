<?php
use AspectMock\Test as test;

use Robo\Traits\Common\AdjustQuotes;

class GulpTest extends \Codeception\TestCase\Test
{
    use AdjustQuotes;

    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $baseGulp;

    protected function _before()
    {
        $this->baseGulp = test::double('Robo\Task\Gulp\Base', [
            'output' => new \Symfony\Component\Console\Output\NullOutput()
        ]);
    }

    // tests
    public function testGulpGetCommand()
    {
        $this->assertEquals(
            $this->adjustQuotes("gulp 'default'"),
            (new \Robo\Task\Gulp\Run('default','gulp'))->getCommand()
        );

        $this->assertEquals(
            $this->adjustQuotes("gulp 'another'"),
            (new \Robo\Task\Gulp\Run('another','gulp'))->getCommand()
        );

        $this->assertEquals(
            $this->adjustQuotes("gulp 'default' --silent"),
            (new \Robo\Task\Gulp\Run('default','gulp'))->silent()->getCommand()
        );

        $this->assertEquals(
            $this->adjustQuotes("gulp 'default' --no-color"),
            (new \Robo\Task\Gulp\Run('default','gulp'))->noColor()->getCommand()
        );

        $this->assertEquals(
            $this->adjustQuotes("gulp 'default' --color"),
            (new \Robo\Task\Gulp\Run('default','gulp'))->color()->getCommand()
        );

        $this->assertEquals(
            $this->adjustQuotes("gulp 'default' --tasks-simple"),
            (new \Robo\Task\Gulp\Run('default','gulp'))->simple()->getCommand()
        );
    }

    public function testGulpRun()
    {
        $gulp = test::double('Robo\Task\Gulp\Run', ['executeCommand' => null, 'getConfig' => new \Robo\Config(), 'logger' => new \Psr\Log\NullLogger()]);

        $task = (new \Robo\Task\Gulp\Run('default','gulp'))->simple();
        $this->assertEquals(
            $this->adjustQuotes("gulp 'default' --tasks-simple"),
            $task->getCommand());
        $task->run();
        $gulp->verifyInvoked('executeCommand', [$this->adjustQuotes("gulp 'default' --tasks-simple")]);
    }

    public function testGulpUnusualChars()
    {
        $isWindows = defined('PHP_WINDOWS_VERSION_MAJOR');
        $expected = $isWindows ?
            'gulp "anotherWith weired!(\"\') Chars"' :
            "gulp 'anotherWith weired!(\"'\\'') Chars'";

        $this->assertEquals(
            $expected,
            (new \Robo\Task\Gulp\Run('anotherWith weired!("\') Chars','gulp'))->getCommand()
        );
    }
}
