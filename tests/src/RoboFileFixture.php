<?php

namespace Robo;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;
use Consolidation\AnnotatedCommand\Events\CustomEventAwareInterface;
use Consolidation\AnnotatedCommand\Events\CustomEventAwareTrait;
use Consolidation\OutputFormatters\StructuredData\PropertyList;
use Robo\Contract\VerbosityThresholdInterface;
use Robo\Symfony\ConsoleIO;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * RoboFile under test: a fixture containing some commands to use with tests.
 */
class RoboFileFixture extends \Robo\Tasks implements LoggerAwareInterface, CustomEventAwareInterface
{
    use LoggerAwareTrait;
    use CustomEventAwareTrait;

    /**
     * Demonstrate Robo variable argument passing.
     *
     * @param array $a
     *   A list of commandline parameters.
     */
    public function testArrayArgs(ConsoleIO $io, array $a)
    {
        $io->writeln("The parameters passed are:\n" . var_export($a, true));
    }

    /**
     * Demonstrate use of SymfonyStyle
     *
     * @deprecated Use style injector
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
     * Demonstrate use of SymfonyStyle with a style injector
     */
    public function testStyleInjector(SymfonyStyle $io)
    {
        $io->title('My Title');
        $io->section('Section 1');
        $io->text('Some text in section one printed via injected io object.');
        $io->comment('This is just an example of different styles.');
        $io->section('Section 2');
        $io->text('Some text in section two.');
    }

    /**
     * @hook command-event test:command-event
     */
    public function hookCommandEvent()
    {
        // TODO: Can command events receive $io?
        $this->io()->text('This is the command-event hook for the test:command-event command.');
    }

    public function testCommandEvent(ConsoleIO $io)
    {
        $io->text('This is the main method for the test:command-event command.');
    }

    /**
     * @hook post-command test:command-event
     */
    public function hookPostCommand()
    {
        // TODO: Can post-command events receive $io?
        $this->io()->text('This is the post-command hook for the test:command-event command.');
    }

    /**
     * This command uses a custom event 'custom-event' to collect data.  Note that
     * the event handlers will not be found unless the hook manager is
     * injected into this command handler object via `setHookManager()`
     * (defined in CustomEventAwareTrait). The Robo DI container does this
     * for us through inflection.
     *
     * @command test:custom-event
     */
    public function testCustomEvent()
    {
        $myEventHandlers = $this->getCustomEventHandlers('custom-event');
        $result = [];
        foreach ($myEventHandlers as $handler) {
            $result[] = $handler();
        }
        sort($result);
        return implode(',', $result);
    }

    /**
     * @hook on-event custom-event
     */
    public function hookOne()
    {
        return 'one';
    }

    /**
     * @hook on-event custom-event
     */
    public function hookTwo()
    {
        return 'two';
    }

    /**
     * Test handling of options
     *
     * @field-labels
     *   a: A
     *   b: B
     */
    public function testSimpleList($options = ['a' => '1', 'b' => '2', 'format' => 'yaml'])
    {
        $result = ['a' => $options['a'], 'b' => $options['b']];
        return new PropertyList($result);
    }

    /**
     * Demonstrate Robo error output and command failure.
     */
    public function testError(ConsoleIO $io)
    {
        $io->text(var_export(\Robo\Robo::config()->export(), true));
        return $this->collectionBuilder($io)->taskExec('ls xyzzy' . date('U'))->dir('/tmp')->run();
    }

    public function testExec(ConsoleIO $io)
    {
        return $this->collectionBuilder($io)->taskExec('pwd')->run();
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

    public function testStopOnFail(ConsoleIO $io)
    {
        $this->stopOnFail();
        $this->collectionBuilder($io)
            ->taskExec('ls xyzzy' . date('U'))
                ->dir('/tmp')
            ->run();

        // stopOnFail() should cause the failed task to throw an exception,
        // so we should not get here, and instead exit the program with a
        // non-zero status.
        return 0;
    }

    public function testVerbosity(ConsoleIO $io)
    {
        $io->writeln('This command will print more information at higher verbosity levels.');
        $io->writeln('Try running with -v, -vv or -vvv');
        $io->writeln('The current verbosity level is ' . $io->getVerbosity());
        $io->writeln('This is a verbose message (-v).', OutputInterface::VERBOSITY_VERBOSE);
        $io->writeln('This is a very verbose message (-vv).', OutputInterface::VERBOSITY_VERY_VERBOSE);
        $io->writeln('This is a debug message (-vvv).', OutputInterface::VERBOSITY_DEBUG);

        if ($this->logger) {
            $this->logger->warning('This is a warning log message.');
            $this->logger->notice('This is a notice log message.');
            $this->logger->debug('This is a debug log message.');
        }
    }

    public function testVerbosityThreshold(ConsoleIO $io)
    {
        $io->writeln('This command will print more information at higher verbosity levels.');
        $io->writeln('Try running with -v, -vv or -vvv');

        return $this->collectionBuilder($io)
            ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_VERBOSE)
            ->taskExec('echo verbose or higher')
                ->interactive(false)
            ->taskExec('echo very verbose or higher')
                ->interactive(false)
                ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_VERY_VERBOSE)
            ->taskExec('echo always printed')
                ->interactive(false)
                ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_NORMAL)
            ->run();
    }

    // This tests commands that still use the old API rather than passing a ConsoleIO parameter.
    public function testVerbosityThresholdCompatability()
    {
        $this->output()->writeln('This command will print more information at higher verbosity levels.');
        $this->output()->writeln('Try running with -v, -vv or -vvv');

        return $this->collectionBuilder()
            ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_VERBOSE)
            ->taskExec('echo verbose or higher')
                ->interactive(false)
            ->taskExec('echo very verbose or higher')
                ->interactive(false)
                ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_VERY_VERBOSE)
            ->taskExec('echo always printed')
                ->interactive(false)
                ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_NORMAL)
            ->run();
    }

    public function testDeploy(ConsoleIO $io)
    {
        $gitTask = $this->collectionBuilder($io)
            ->taskGitStack()
                ->pull();

        $this->collectionBuilder($io)
            ->taskSshExec('mysite.com')
                ->remoteDir('/var/www/somesite')
                ->exec($gitTask)
                ->run();
    }

    /**
     * Demonstrate use of Symfony $input object in Robo in place of
     * the usual "parameter arguments".
     *
     * @param InputInterface $input
     * @arg array $a A list of commandline parameters.
     * @option foo
     * @default a []
     * @default foo []
     */
    public function testSymfony(ConsoleIO $io, InputInterface $input)
    {
        // Note that $input is also available via $io
        $a = $input->getArgument('a');
        $io->writeln("The parameters passed are:\n" . var_export($a, true));
        $foo = $input->getOption('foo');
        if (!empty($foo)) {
            $io->writeln("The options passed via --foo are:\n" . var_export($foo, true));
        }
    }
}
