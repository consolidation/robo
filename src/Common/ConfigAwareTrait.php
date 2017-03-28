<?php

namespace Robo\Common;

use Robo\Robo;
use Robo\Config\Config;

trait ConfigAwareTrait
{
    /**
     * @var \Robo\Config\Config
     */
    protected $config;

    /**
     * Set the config management object.
     *
     * @param \Robo\Config\Config $config
     *
     * @return $this
     *
     * @see \Robo\Contract\ConfigAwareInterface::setConfig()
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get the config management object.
     *
     * @return \Robo\Config\Config
     *
     * @see \Robo\Contract\ConfigAwareInterface::getConfig()
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Any class that uses ConfigAwareTrait SHOULD override this method
     * , and define a prefix for its configuration items. This is usually
     * done in a base class; see BaseTask::configPrefix(). It is not
     * necessary to override this method for classes that have no configuration
     * items of their own.
     *
     * @return string
     */
    protected static function configPrefix()
    {
        return '';
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private static function getClassKey($key)
    {
        return sprintf('%s%s.%s', static::configPrefix(), get_called_class(), $key);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param Config|null $config
     */
    public static function configure($key, $value, $config = null)
    {
        if (!$config) {
            $config = Robo::config();
        }
        $config->setDefault(static::getClassKey($key), $value);
    }

    /**
     * @param string $key
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    protected function getConfigValue($key, $default = null)
    {
        if (!$this->getConfig()) {
            return $default;
        }
        return $this->getConfig()->get(static::getClassKey($key), $default);
    }
}
