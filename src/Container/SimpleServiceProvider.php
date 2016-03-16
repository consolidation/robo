<?php

namespace Robo\Container;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * This works like a League\Container\AbstractServiceProvider, except that
 * the $provides array should contain an associative array, where the key
 * for each element is the service alias, and its corresponding value
 * is the name of the implementing service class.
 */
abstract class SimpleServiceProvider implements ServiceProviderInterface
{
    use ContainerAwareTrait;

    public function __construct($provides = [])
    {
        $this->provides += $provides;
    }

    /**
     * @var array
     */
    protected $provides = [];

    /**
     * {@inheritdoc}
     */
    public function provides($alias = null)
    {
        if (! is_null($alias)) {
            return (array_key_exists($alias, $this->provides));
        }

        return array_keys($this->provides);
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        foreach ($this->provides as $alias => $concrete) {
            $this->getContainer()->add($alias, $concrete);
        }
    }
}
