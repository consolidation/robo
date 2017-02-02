<?php
namespace Robo\Common;

use Robo\Contract\OutputAdapterInterface;
use Robo\Contract\OutputAwareInterface;
use Robo\Contract\VerbosityThresholdInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Adapt OutputInterface or other output function to the VerbosityThresholdInterface.
 */
class OutputAdapter implements OutputAdapterInterface, OutputAwareInterface
{
    use OutputAwareTrait;

    protected $verbosityMap = [
        VerbosityThresholdInterface::VERBOSITY_NORMAL => OutputInterface::VERBOSITY_NORMAL,
        VerbosityThresholdInterface::VERBOSITY_VERBOSE => OutputInterface::VERBOSITY_VERBOSE,
        VerbosityThresholdInterface::VERBOSITY_VERY_VERBOSE => OutputInterface::VERBOSITY_VERY_VERBOSE,
        VerbosityThresholdInterface::VERBOSITY_DEBUG => OutputInterface::VERBOSITY_DEBUG,
    ];

    public function verbosityMeetsThreshold($verbosityThreshold)
    {
        if (!isset($this->verbosityMap[$verbosityThreshold])) {
            return true;
        }
        $verbosityThreshold = $this->verbosityMap[$verbosityThreshold];
        $verbosity = $this->output()->getVerbosity();

        return $verbosity >= $verbosityThreshold;
    }

    public function writeMessage($message)
    {
        $this->output()->write($message);
    }
}
