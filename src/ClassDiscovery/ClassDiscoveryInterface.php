<?php

namespace Robo\ClassDiscovery;

/**
 * Interface ClassDiscoveryInterface
 *
 * @package Robo\Plugin\ClassDiscovery
 */
interface ClassDiscoveryInterface
{
    /**
     * @param $searchPattern
     *
     * @return $this
     */
    public function setSearchPattern($searchPattern);

    /**
     * @return string[]
     */
    public function getClasses();

    /**
     * @param $class
     *
     * @return string|null
     */
    public function getFile($class);
}
