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
     * @return mixed
     */
    public function setSearchPattern($searchPattern);

    /**
     * @return string[]
     */
    public function getClasses();

    /**
     * @param $class
     *
     * @return mixed
     */
    public function getFile($class);
}
