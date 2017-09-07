<?php

namespace Robo\RoboPlugin\Commands;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

use Consolidation\AnnotatedCommand\Events\CustomEventAwareInterface;
use Consolidation\AnnotatedCommand\Events\CustomEventAwareTrait;
use Consolidation\OutputFormatters\StructuredData\PropertyList;
use Robo\Contract\VerbosityThresholdInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Example Robo plugin commandfile
 */
class RoboTestPluginCommands extends \Robo\Tasks implements LoggerAwareInterface, CustomEventAwareInterface
{
    use LoggerAwareTrait;
    use CustomEventAwareTrait;

    /**
     * @command test:hello
     */
    public function testHello(array $a)
    {
        $this->say("Hello to: " . implode(',', $a));
    }
}
