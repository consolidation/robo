<?php

namespace Robo\Contract;

use Consolidation\Config\ConfigInterface;

interface ConfigAwareInterface
{
    /**
     * Set the config reference
     *
     * @param ConfigInterface $config
     *
     * @return $this
     */
    public function setConfig(ConfigInterface $config);

    /**
     * Get the config reference
     *
     * @return ConfigInterface
     */
    public function getConfig();
}
