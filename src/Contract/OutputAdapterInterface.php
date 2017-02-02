<?php
namespace Robo\Contract;

/**
 * Adapt OutputInterface or other output function to the VerbosityThresholdInterface.
 */
interface OutputAdapterInterface
{
    public function verbosityMeetsThreshold($verbosityThreshold);
    public function writeMessage($message);
}
