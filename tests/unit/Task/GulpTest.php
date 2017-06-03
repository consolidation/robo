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
        verify(
            (new \Robo\Task\Gulp\Run('default','gulp'))->getCommand()
        )->equals($this->adjustQuotes("gulp 'default'"));

        verify(
            (new \Robo\Task\Gulp\Run('another','gulp'))->getCommand()
        )->equals($this->adjustQuotes("gulp 'another'"));

        verify(
            (new \Robo\Task\Gulp\Run('default','gulp'))->silent()->getCommand()
        )->equals($this->adjustQuotes("gulp 'default' --silent"));

        verify(
            (new \Robo\Task\Gulp\Run('default','gulp'))->noColor()->getCommand()
        )->equals($this->adjustQuotes("gulp 'default' --no-color"));

        verify(
            (new \Robo\Task\Gulp\Run('default','gulp'))->color()->getCommand()
        )->equals($this->adjustQuotes("gulp 'default' --color"));

        verify(
            (new \Robo\Task\Gulp\Run('default','gulp'))->simple()->getCommand()
        )->equals($this->adjustQuotes("gulp 'default' --tasks-simple"));
    }

    public function testGulpRun()
    {
        $gulp = test::double('Robo\Task\Gulp\Run', ['executeCommand' => null, 'getConfig' => new \Robo\Config(), 'logger' => new \Psr\Log\NullLogger()]);

        $task = (new \Robo\Task\Gulp\Run('default','gulp'))->simple();
        verify($task->getCommand())->equals($this->adjustQuotes("gulp 'default' --tasks-simple"));
        $task->run();
        $gulp->verifyInvoked('executeCommand', [$this->adjustQuotes("gulp 'default' --tasks-simple")]);
    }

    public function testGulpUnusualChars()
    {
        $isWindows = defined('PHP_WINDOWS_VERSION_MAJOR');

        if ($isWindows) {

            verify(
                (new \Robo\Task\Gulp\Run('anotherWith weired!("\') Chars','gulp'))->getCommand()
            )->equals('gulp "anotherWith weired!(\"\') Chars"');

        } else {

            verify(
                (new \Robo\Task\Gulp\Run('anotherWith weired!("\') Chars','gulp'))->getCommand()
            )->equals("gulp 'anotherWith weired!(\"'\\'') Chars'");

        }
    }
}
