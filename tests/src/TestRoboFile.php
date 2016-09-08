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
     * Demonstrate use of SymfonyStyle
     */
    public function testSymfonyStyle()
    {
        $this->io()->title('My Title');
        $this->io()->section('Section 1');
        $this->io()->text('Some text in section one.');
        $this->io()->comment('This is just an example of different styles.');
        $this->io()->section('Section 2');
        $this->io()->text('Some text in section two.');
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

    public function testStopOnFail()
    {
        $this->stopOnFail();
        $this->collectionBuilder()
            ->taskExec('ls xyzzy' . date('U'))
                ->dir('/tmp')
            ->run();

        // stopOnFail() should cause the failed task to throw an exception,
        // so we should not get here, and instead exit the program with a
        // non-zero status.
        return 0;
    }
}
