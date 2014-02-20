<?php
namespace Robo\Task;
use \Robo\Result;

interface TaskInterface {

    /**
     * @return \Robo\Result
     */
    function run();
}
