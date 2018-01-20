<?php

namespace Robo\Plugin\Definition;

/**
 * Robo plugin definition interface.
 */
interface PluginDefinitionInterface
{
    /**
     * Unique plugin identifier, used as service container ID.
     *
     * @return string
     */
    public function getId();

    /**
     * Get plugin class name.
     *
     * @return string
     */
    public function getClass();
}
