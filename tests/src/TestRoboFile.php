<?php

namespace Robo;

use Robo\Result;
use Robo\ResultData;
use Robo\Collection\CollectionBuilder;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;

/**
 * Test RoboFile.
 */
class TestRoboFile extends \Robo\Tasks
{
    /**
     * Demonstrate Robo variable argument passing.
     *
     * @param $a A list of commandline parameters.
     */
    public function testArrayArgs(array $a)
    {
        $this->say("The parameters passed are:\n" . var_export($a, true));
    }

    /**
     * Demonstrate Robo error output and command failure.
     */
    public function testError()
    {
        return $this->taskExec('ls xyzzy' . date('U'))->dir('/tmp')->run();
    }

    /**
     * Demonstrate what happens when a command or a task
     * throws an exception.  Note that typically, Robo commands
     * should return Result objects rather than throw exceptions.
     */
    public function testException($options = ['task' => false])
    {
        if (!$options['task']) {
            throw new \RuntimeException('Command failed with an exception.');
        }
        throw new \RuntimeException('Task failed with an exception.');
    }
}
