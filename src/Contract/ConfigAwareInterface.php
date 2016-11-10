<?php

namespace Robo\Contract;

use Robo\Config;

interface ConfigAwareInterface
{
    /**
     * Set the config reference
     *
     * @param \Robo\Config $config
     *
     * @return $this
     */
    public function setConfig(Config $config);

    /**
     * Get the config reference
     *
     * @return \Robo\Config
     */
    public function getConfig();
}
