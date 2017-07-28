<?php

namespace Robo\Common;

use Robo\Robo;
use Consolidation\Config\ConfigInterface;

trait ConfigAwareTrait
{
    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * Set the config management object.
     *
     * @param ConfigInterface $config
     *
     * @return $this
     *
     * @see \Robo\Contract\ConfigAwareInterface::setConfig()
     */
    public function setConfig(ConfigInterface $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get the config management object.
     *
     * @return ConfigInterface
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
     * done in a base class. When used, this method should return a string
     * that ends with a "."; see BaseTask::configPrefix().
     *
     * @return string
     */
    protected static function configPrefix()
    {
        return '';
    }

    protected static function configClassIdentifier($classname)
    {
        $configIdentifier = strtr($classname, '\\', '.');
        $configIdentifier = preg_replace('#^(.*\.Task\.|\.)#', '', $configIdentifier);

        return $configIdentifier;
    }

    protected static function configPostfix()
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
        $configPrefix = static::configPrefix();                            // task.
        $configClass = static::configClassIdentifier(get_called_class());  // PARTIAL_NAMESPACE.CLASSNAME
        $configPostFix = static::configPostfix();                          // .settings

        return sprintf('%s%s%s.%s', $configPrefix, $configClass, $configPostFix, $key);
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
