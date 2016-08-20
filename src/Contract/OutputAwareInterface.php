<?php

/**
 * Provide OutputAwareInterface, not present in Symfony Console
 */

namespace Robo\Contract;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * InputAwareInterface should be implemented by classes that depends on the
 * Console Input.
 *
 * @author Wouter J <waldio.webdesign@gmail.com>
 */
interface OutputAwareInterface
{
    /**
     * Sets the Console Output.
     *
     * @param OutputInterface
     */
    public function setOutput(OutputInterface $output);
}
