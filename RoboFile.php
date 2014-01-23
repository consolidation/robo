<?php
class RoboFile
{
    use \Robo\Add\Output;
    /**
     * @description Hello world
     */
    public function hello($name)
    {
        $this->say("hello $name");
    }
}