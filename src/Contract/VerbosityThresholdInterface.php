<?php
namespace Robo\Contract;

use Robo\Contract\OutputAdapterInterface;

/**
 * Record and determine whether the current verbosity level exceeds the
 * desired threshold level to produce output.
 */
interface VerbosityThresholdInterface
{
    const VERBOSITY_NORMAL = 1;
    const VERBOSITY_VERBOSE = 2;
    const VERBOSITY_VERY_VERBOSE = 3;
    const VERBOSITY_DEBUG = 4;

    public function setVerbosityThreshold($verbosityThreshold);
    public function verbosityThreshold();
    public function setOutputAdapter(OutputAdapterInterface $outputAdapter);
    public function outputAdapter();
    public function hasOutputAdapter();
    public function verbosityMeetsThreshold();
    public function writeMessage($message);
}
