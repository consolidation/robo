<?php

namespace Robo\Common;

use Robo\Robo;
use Robo\Config;

trait ConfigAwareTrait
{
    /**
     * @var \Robo\Config
     */
    protected $config;

    /**
     * Set the config management object.
     *
     * @param  \Robo\Config $config
     * @return $this
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get the config management object.
     *
     * @return \Robo\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    private static function getClassKey($key)
    {
        return sprintf('%s.%s', get_called_class(), $key);
    }

    /**
     * @deprecated
     */
    public static function configure($key, $value)
    {
        Robo::config()->set(static::getClassKey($key), $value);
    }

    protected function getConfigValue($key, $default = null)
    {
        if (!$this->getConfig()) {
            return $default;
        }
        return $this->getConfig()->get(static::getClassKey($key), $default);
    }
}
