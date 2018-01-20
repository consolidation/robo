<?php

namespace Robo\Plugin\Discovery;

/**
 * Plugin discovery interface.
 *
 * @package Robo\Plugin\Discovery
 */
interface DiscoveryInterface
{
    /**
     * @param string $pluginId
     *
     * @return \Robo\Plugin\Definition\PluginDefinition
     */
    public function getDefinition($pluginId);

    /**
     * @return \Robo\Plugin\Definition\PluginDefinition[]
     */
    public function getDefinitions();

    /**
     * @param string $pluginId
     *
     * @return bool
     */
    public function hasDefinition($pluginId);
}
