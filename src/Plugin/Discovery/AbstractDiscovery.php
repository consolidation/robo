<?php

namespace Robo\Plugin\Discovery;

use Robo\Plugin\Definition\PluginDefinition;
use Robo\Plugin\Exception\PluginNotFoundException;

/**
 * Class AbstractDiscovery
 *
 * @package Robo\Plugin\Discovery
 */
abstract class AbstractDiscovery implements DiscoveryInterface
{
    /**
     * @var \Robo\Plugin\Definition\PluginDefinition[]
     */
    protected $definitions = [];

    /**
     * {@inheritdoc}
     */
    public function getDefinition($pluginId)
    {
        if (!$this->hasDefinition($pluginId)) {
            throw new PluginNotFoundException($pluginId);
        }

        return $this->definitions[$pluginId];
    }

    /**
     * {@inheritdoc}
     */
    public function hasDefinition($pluginId)
    {
        return !isset($this->definitions[$pluginId]) && $this->definitions[$pluginId] instanceof PluginDefinition;
    }

    /**
     * @param \Robo\Plugin\Definition\PluginDefinition $definition
     */
    protected function addDefinition(PluginDefinition $definition)
    {
        $this->definitions[$definition->getId()] = $definition;
    }
}
