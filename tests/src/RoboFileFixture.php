<?php

namespace Robo;

use Robo\Result;
use Robo\ResultData;
use Robo\Collection\CollectionBuilder;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * RoboFile under test: a fixture containing some commands to use with tests.
 */
class RoboFileFixture extends \Robo\Tasks implements LoggerAwareInterface
{
    use LoggerAwareTrait;

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
     * @hook command-event test:command-event
     */
    public function hookCommandEvent()
    {
        $this->io()->text('This is the command-event hook for the test:command-event command.');
    }

    public function testCommandEvent()
    {
        $this->io()->text('This is the main method for the test:command-event command.');
    }

    /**
     * @hook post-command test:command-event
     */
    public function hookPostCommand()
    {
        $this->io()->text('This is the post-command hook for the test:command-event command.');
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

    public function testVerbosity()
    {
        $this->output()->writeln('This command will print more information at higher verbosity levels.');
        $this->output()->writeln('Try running with -v, -vv or -vvv');
        $this->output()->writeln('The current verbosity level is ' . $this->output()->getVerbosity());
        $this->output()->writeln('This is a verbose message (-v).', OutputInterface::VERBOSITY_VERBOSE);
        $this->output()->writeln('This is a very verbose message (-vv).', OutputInterface::VERBOSITY_VERY_VERBOSE);
        $this->output()->writeln('This is a debug message (-vvv).', OutputInterface::VERBOSITY_DEBUG);
        $this->logger->warning('This is a warning log message.');
        $this->logger->notice('This is a notice log message.');
        $this->logger->debug('This is a debug log message.');
    }

    public function testDeploy()
    {
        $gitTask = $this->taskGitStack()
            ->pull();

        $this->taskSshExec('mysite.com')
            ->remoteDir('/var/www/somesite')
            ->exec($gitTask)
            ->run();
    }
}
