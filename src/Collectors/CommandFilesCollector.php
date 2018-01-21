<?php

namespace Robo\Collectors;

use Robo\ClassDiscovery\ClassDiscoveryInterface;

/**
 * Class CommandFilesCollector
 *
 * @package Robo\Collectors
 */
class CommandFilesCollector
{
    /**
     * @var \Robo\ClassDiscovery\ClassDiscoveryInterface
     */
    protected $classDiscovery;

    /**
     * CommandFilesCollector constructor.
     *
     * @param \Robo\ClassDiscovery\ClassDiscoveryInterface $classDiscovery
     */
    public function __construct(ClassDiscoveryInterface $classDiscovery)
    {
        $this->classDiscovery = $classDiscovery;
    }
}
