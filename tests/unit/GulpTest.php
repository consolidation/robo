<?php
use AspectMock\Test as test;

class GulpTest extends \Codeception\TestCase\Test
{
    use \Robo\Task\Gulp\loadTasks;

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
        verify(
            $this->taskGulpRun('default','gulp')->getCommand()
        )->equals('gulp \'default\'');
        
        verify(
            $this->taskGulpRun('another','gulp')->getCommand()
        )->equals('gulp \'another\'');

        verify(
            $this->taskGulpRun('anotherWith wired!("\') Chars','gulp')->getCommand()
        )->equals("gulp 'anotherWith wired!(\"'\\'') Chars'");


        verify(
            $this->taskGulpRun('default','gulp')->silent()->getCommand()
        )->equals('gulp \'default\' --silent');

        verify(
            $this->taskGulpRun('default','gulp')->noColor()->getCommand()
        )->equals('gulp \'default\' --no-color');

        verify(
            $this->taskGulpRun('default','gulp')->color()->getCommand()
        )->equals('gulp \'default\' --color');

        verify(
            $this->taskGulpRun('default','gulp')->simple()->getCommand()
        )->equals('gulp \'default\' --tasks-simple');





    }

}