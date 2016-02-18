<?php
namespace Robo\Contract;

use Robo\Result;

/**
 * Log the result of a Robo task.
 *
 * Interface LogResultInterface
 * @package Robo\Contract
 */
interface LogResultInterface
{
    /**
     * Print the result of an operation
     *
     * @return \Robo\Result
     */
    public function logResult(Result $result);
}
