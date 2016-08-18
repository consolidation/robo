<?php
use AspectMock\Test as test;
use Robo\Robo;

class GulpTest extends \Codeception\TestCase\Test
{
    /**
     * @var \AspectMock\Proxy\ClassProxy
     */
    protected $baseGulp;

    protected function _before()
    {
        $this->baseGulp = test::double('Robo\Task\Gulp\Base', [
            'getOutput' => new \Symfony\Component\Console\Output\NullOutput()
        ]);
    }

    // tests
    public function testGulpRun()
    {
        $isWindows = defined('PHP_WINDOWS_VERSION_MAJOR');

        if ($isWindows) {
            verify(
                (new \Robo\Task\Gulp\Run())
                (new \Robo\Task\Gulp\Run('default','gulp'))->getCommand()
            )->equals('gulp "default"');

            verify(
                (new \Robo\Task\Gulp\Run('another','gulp'))->getCommand()
            )->equals('gulp "another"');

            verify(
                (new \Robo\Task\Gulp\Run('anotherWith weired!("\') Chars','gulp'))->getCommand()
            )->equals('gulp "anotherWith weired!(\"\') Chars"');

            verify(
                (new \Robo\Task\Gulp\Run('default','gulp'))->silent()->getCommand()
            )->equals('gulp "default" --silent');

            verify(
                (new \Robo\Task\Gulp\Run('default','gulp'))->noColor()->getCommand()
            )->equals('gulp "default" --no-color');

            verify(
                (new \Robo\Task\Gulp\Run('default','gulp'))->color()->getCommand()
            )->equals('gulp "default" --color');

            verify(
                (new \Robo\Task\Gulp\Run('default','gulp'))->simple()->getCommand()
            )->equals('gulp "default" --tasks-simple');

        } else {

            verify(
                (new \Robo\Task\Gulp\Run('default','gulp'))->getCommand()
            )->equals('gulp \'default\'');

            verify(
                (new \Robo\Task\Gulp\Run('another','gulp'))->getCommand()
            )->equals('gulp \'another\'');

            verify(
                (new \Robo\Task\Gulp\Run('anotherWith weired!("\') Chars','gulp'))->getCommand()
            )->equals("gulp 'anotherWith weired!(\"'\\'') Chars'");

            verify(
                (new \Robo\Task\Gulp\Run('default','gulp'))->silent()->getCommand()
            )->equals('gulp \'default\' --silent');

            verify(
                (new \Robo\Task\Gulp\Run('default','gulp'))->noColor()->getCommand()
            )->equals('gulp \'default\' --no-color');

            verify(
                (new \Robo\Task\Gulp\Run('default','gulp'))->color()->getCommand()
            )->equals('gulp \'default\' --color');

            verify(
                (new \Robo\Task\Gulp\Run('default','gulp'))->simple()->getCommand()
            )->equals('gulp \'default\' --tasks-simple');
        }
    }
}
