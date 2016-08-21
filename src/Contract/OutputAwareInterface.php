<?php

/**
 * Provide OutputAwareInterface, not present in Symfony Console
 */

namespace Robo\Contract;

use Symfony\Component\Console\Output\OutputInterface;

interface OutputAwareInterface
{
    /**
     * Sets the Console Output.
     *
     * @param OutputInterface
     */
    public function setOutput(OutputInterface $output);
}
