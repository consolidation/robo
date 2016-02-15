<?php

/*
 * This file may be submitted to the Symfony package, which is:
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Robo\Common; // maybe: namespace Symfony\Component\Console\Logger;

use Robo\Common\ConsoleLogLevel; // maybe: use Symfony\Component\Console\ConsoleLogLevel;
// use Symfony\Component\Console\Logger\ConsoleLogger;

use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\StringInput;

/**
 * Extend Symfony\Component\Console\Logger\ConsoleLogger
 * so that each of the different log level messages are
 * routed through the corresponding SymfonyStyle formatting
 * method.  Log messages are always sent to stderr, so the
 * provided output object must implement ConsoleOutputInterface.
 *
 * @author Greg Anderson <greg.1.anderson@greenknowe.org>
 */
class StyledConsoleLogger extends AbstractLogger // extends ConsoleLogger
{
    protected $outputStyler;
    protected $errorStyler;

    /**
     * @param OutputInterface $output
     * @param array           $verbosityLevelMap
     * @param array           $formatLevelMap
     */
    public function __construct(OutputInterface $output, array $verbosityLevelMap = array(), array $formatLevelMap = array())
    {
        // parent::__construct($output, $verbosityLevelMap, $formatLevelMap);

        $this->output = $output;
        $this->verbosityLevelMap = $verbosityLevelMap + $this->verbosityLevelForConsoleApplicationsMap + $this->verbosityLevelMap;
        $this->formatLevelMap = $formatLevelMap + $this->formatLevelMap;
    }

    protected function createStyler(OutputInterface $output)
    {
        // It is a little odd that SymfonyStyle & c. mix input and output
        // functions. We only need the output methods here, so create a
        // stand-in input object to satisfy the SymfonyStyle constructor.
        $nullInput = new StringInput('');
        $styler = new SymfonyStyle($nullInput, $output);
        $styler->setVerbosity($output->getVerbosity());

        return $styler;
    }

    public function getOutputStyler()
    {
        if (!isset($this->outputStyler)) {
            $this->outputStyler = $this->createStyler($this->output);
        }
        return $this->outputStyler;
    }

    public function getErrorStyler()
    {
        if (true || !$this->output instanceof ConsoleOutputInterface) {
            return $this->getOutputStyler();
        }
        if (!isset($this->errorStyler)) {
            $this->errorStyler = $this->createStyler($this->output->getErrorOutput());
        }
        return $this->errorStyler;
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = array())
    {
        if (!isset($this->verbosityLevelMap[$level])) {
            throw new InvalidArgumentException(sprintf('The log level "%s" does not exist.', $level));
        }

        // Write to the error output if necessary and available
        if ($this->formatLevelMap[$level] === self::ERROR) {
            $output = $this->getErrorStyler();
        } else {
            $output = $this->getOutputStyler();
        }

        if ($output->getVerbosity() >= $this->verbosityLevelMap[$level]) {
            $formatFunction = 'writeln';
            if (array_key_exists($level, $this->formatFunctionMap)) {
                $formatFunction = $this->formatFunctionMap[$level];
            }
            $output->$formatFunction($this->interpolate($message, $context));
        }
    }

    public function success($message, array $context = array())
    {
        $this->log(ConsoleLogLevel::SUCCESS, $message, $context);
    }

    // The functions below could be eliminated if made `protected` intead
    // of `private` in ConsoleLogger

    const INFO = 'info';
    const ERROR = 'error';

    /**
     * @var OutputInterface
     */
    private $output;
    /**
     * @var array
     */
    private $verbosityLevelMap = array(
        LogLevel::EMERGENCY => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::ALERT => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::CRITICAL => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::ERROR => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::WARNING => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::NOTICE => OutputInterface::VERBOSITY_VERBOSE,
        LogLevel::INFO => OutputInterface::VERBOSITY_VERY_VERBOSE,
        LogLevel::DEBUG => OutputInterface::VERBOSITY_DEBUG,
        ConsoleLogLevel::OK => OutputInterface::VERBOSITY_NORMAL,
        ConsoleLogLevel::SUCCESS => OutputInterface::VERBOSITY_NORMAL,
    );

    protected $verbosityLevelForConsoleApplicationsMap = array(
        LogLevel::NOTICE => OutputInterface::VERBOSITY_NORMAL,
    );

    /**
     * @var array
     */
    private $formatLevelMap = array(
        LogLevel::EMERGENCY => self::ERROR,
        LogLevel::ALERT => self::ERROR,
        LogLevel::CRITICAL => self::ERROR,
        LogLevel::ERROR => self::ERROR,
        LogLevel::WARNING => self::INFO,
        LogLevel::NOTICE => self::INFO,
        LogLevel::INFO => self::INFO,
        LogLevel::DEBUG => self::INFO,
        ConsoleLogLevel::OK => self::INFO,
        ConsoleLogLevel::SUCCESS => self::INFO,
    );

    private $formatFunctionMap = array(
        LogLevel::EMERGENCY => 'error',
        LogLevel::ALERT => 'error',
        LogLevel::CRITICAL => 'error',
        LogLevel::ERROR => 'error',
        LogLevel::WARNING => 'warning',
        LogLevel::NOTICE => 'note',
        LogLevel::INFO => 'note',
        LogLevel::DEBUG => 'note',
        ConsoleLogLevel::OK => 'success',
        ConsoleLogLevel::SUCCESS => 'success',
    );

    /**
     * Interpolates context values into the message placeholders.
     *
     * @author PHP Framework Interoperability Group
     *
     * @param string $message
     * @param array  $context
     *
     * @return string
     */
    private function interpolate($message, array $context)
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace[sprintf('{%s}', $key)] = $val;
            }
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }
}
