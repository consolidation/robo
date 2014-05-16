<?php
use AspectMock\Test as test;

class GruntTest extends \Codeception\TestCase\Test
{
    use \Robo\Task\Grunt;
    
    public function testGrunt()
    {
        $bower = test::double('Robo\Task\GruntTask', ['executeCommand' => null]);
        $this->taskGrunt('grunt')->run();
        $bower->verifyInvoked('executeCommand', ['grunt']);
    }

    public function testGruntCommand()
    {
        verify(
            $this->taskGrunt('grunt')->getCommand()
        )->equals('grunt');

        verify(
            $this->taskGrunt('grunt')->arg('build')->getCommand()
        )->equals('grunt build');
    }

}