<?php

namespace Robo\Contract;

use Composer\Autoload\ClassLoader;

/**
 * Interface ClassLoaderAwareInterface
 *
 * @package Robo\Contract
 */
interface ClassLoaderAwareInterface
{
    /**
     * @param \Composer\Autoload\ClassLoader $classLoader
     *
     * @return $this
     */
    public function setClassLoader(ClassLoader $classLoader);

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public function getClassLoader();
}
