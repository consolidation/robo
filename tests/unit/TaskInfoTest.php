<?php

class TaskInfoTest extends \Codeception\TestCase\Test
{
    protected function infoFor($method)
    {
        return new \Robo\TaskInfo(get_class($this), $method);
    }

    public function testAnnotationDescription()
    {
        verify($this->infoFor('printMe')->getDescription())
            ->equals('Prints something to console');
        verify($this->infoFor('installMe')->getDescription())
            ->equals('installs something');
        verify($this->infoFor('updateMe')->getDescription())
            ->equals('updates something');
    }

    public function testAnnotationName()
    {
        verify($this->infoFor('printMe')->getName())
            ->equals('print:me');

        verify($this->infoFor('installMe')->getName())
            ->equals('install');
    }

    public function testParams()
    {
        $args = $this->infoFor('installMe')->getArguments();
        verify($args)->hasKey('param1');
        verify($args)->hasKey('param2');
        verify($args['param2'])->equals('optional');
    }

    /**
     * Prints something to console
     */
    public function printMe() {}

    /**
     * Really useful method
     * @name install
     * @desc installs something
     */
    public function installMe($param1, $param2 = 'optional') {}

    /**
     * Really useful method
     * @description updates something
     */
    public function updateMe() {}


}