<?php

namespace Robo\Contract;

use Robo\Config\Config;

interface ConfigAwareInterface
{
    /**
     * Set the config reference
     *
     * @param \Robo\Config\Config $config
     *
     * @return $this
     */
    public function setConfig(Config $config);

    /**
     * Get the config reference
     *
     * @return \Robo\Config\Config
     */
    public function getConfig();
}
