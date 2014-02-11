<?php
namespace Robo\Task;

use Robo\Result;

interface TaskInterface {

    /**
     * @return Result
     */
    function run();
}
