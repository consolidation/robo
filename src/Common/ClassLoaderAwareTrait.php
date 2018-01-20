<?php

namespace Robo\Common;

use Composer\Autoload\ClassLoader;

/**
 * Set and get Composer ClassLoader.
 *
 * @package Robo\Common
 */
trait ClassLoaderAwareTrait
{
    /**
     * @var \Composer\Autoload\ClassLoader
     */
    protected $classLoader;

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public function getClassLoader()
    {
        return $this->classLoader;
    }

    /**
     * @param \Composer\Autoload\ClassLoader $classLoader
     *
     * @return $this
     */
    public function setClassLoader(ClassLoader $classLoader)
    {
        $this->classLoader = $classLoader;

        return $this;
    }
}
