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
    protected $stylerClassname;

    protected $formatFunctionMap = array(
        LogLevel::EMERGENCY => 'error',
        LogLevel::ALERT => 'error',
        LogLevel::CRITICAL => 'error',
        LogLevel::ERROR => 'error',
        LogLevel::WARNING => 'warning',
        LogLevel::NOTICE => 'text',
        LogLevel::INFO => 'text',
        LogLevel::DEBUG => 'comment',
        ConsoleLogLevel::OK => 'success',
        ConsoleLogLevel::SUCCESS => 'success',
    );

    /**
     * @param OutputInterface $output
     * @param array           $verbosityLevelMap
     * @param array           $formatLevelMap
     * @param array           $formatFunctionMap
     * @param string          $stylerClassname
     */
    public function __construct(OutputInterface $output, array $verbosityLevelMap = array(), array $formatLevelMap = array(), array $formatFunctionMap = array(), string $stylerClassname = null)
    {
        // parent::__construct($output, $verbosityLevelMap, $formatLevelMap);
        $this->formatFunctionMap = $formatFunctionMap + $this->formatFunctionMap;
        $this->stylerClassname = $stylerClassname;

        $this->output = $output;
        $this->verbosityLevelMap = $verbosityLevelMap + $this->verbosityLevelMap;
        $this->formatLevelMap = $formatLevelMap + $this->formatLevelMap;
    }

    protected function createStyler(OutputInterface $output)
    {
        // If no styler classname was given, create a SymfonyStyle
        if (!$this->stylerClassname) {
            // It is a little odd that SymfonyStyle & c. mix input and output
            // functions. We only need the output methods here, so create a
            // stand-in input object to satisfy the SymfonyStyle constructor.
            $nullInput = new StringInput('');
            $styler = new SymfonyStyle($nullInput, $output);
        }
        else {
            $classname = $this->stylerClassname;
            $styler = new $classname($output);
        }
        $styler->setVerbosity($output->getVerbosity());

        return $styler;
    }

    protected function getOutputStyler()
    {
        if (!isset($this->outputStyler)) {
            $this->outputStyler = $this->createStyler($this->output);
        }
        return $this->outputStyler;
    }

    protected function getErrorStyler()
    {
        if (!$this->output instanceof ConsoleOutputInterface) {
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
        if ($this->formatLevelMap[$level] !== self::ERROR) {
            $outputStyler = $this->getOutputStyler();
        } else {
            $outputStyler = $this->getErrorStyler();
        }

        if ($this->output->getVerbosity() >= $this->verbosityLevelMap[$level]) {
            $formatFunction = 'writeln';
            if (array_key_exists($level, $this->formatFunctionMap)) {
                $formatFunction = $this->formatFunctionMap[$level];
            }
            $outputStyler->$formatFunction($this->interpolate($message, $context));
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

    /**
     * @var array
     */
    private $formatLevelMap = array(
        LogLevel::EMERGENCY => self::ERROR,
        LogLevel::ALERT => self::ERROR,
        LogLevel::CRITICAL => self::ERROR,
        LogLevel::ERROR => self::ERROR,
        LogLevel::WARNING => self::ERROR,
        LogLevel::NOTICE => self::ERROR,
        LogLevel::INFO => self::ERROR,
        LogLevel::DEBUG => self::ERROR,
        ConsoleLogLevel::OK => self::ERROR,
        ConsoleLogLevel::SUCCESS => self::ERROR,
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
