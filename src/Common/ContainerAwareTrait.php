<?php

namespace Robo\Common;

use League\Container\ContainerAwareInterface;
use League\Container\DefinitionContainerInterface;
use League\Container\Exception\ContainerException;

trait ContainerAwareTrait
{
    protected $container;

    public function setContainer(DefinitionContainerInterface $container): ContainerAwareInterface
    {
        $this->container = $container;

        return $this;
    }

    public function getContainer(): DefinitionContainerInterface
    {
        if ($this->container instanceof DefinitionContainerInterface) {
            return $this->container;
        }

        throw new ContainerException('No container implementation has been set.');
    }
}
