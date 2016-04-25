<?php

namespace Robo\Container;

use League\Container\ServiceProvider\AbstractSignatureServiceProvider;

/**
 * This works like a League\Container\AbstractServiceProvider, except that
 * the $provides array should contain an associative array, where the key
 * for each element is the service alias, and its corresponding value
 * is the name of the implementing service class.
 */
class SimpleServiceProvider extends AbstractSignatureServiceProvider
{
    /**
     * @var string
     */
    protected $signature;

    /**
     * @var array
     */
    protected $provides = [];

    public function __construct($provides = [])
    {
        $this->provides += $provides;
        $this->signature = array_keys($provides)[0];
    }

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
