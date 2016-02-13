<?php
namespace Robo\Common;

use Robo\Config;

trait Configuration
{
    private static function getClassKey($key)
    {
        return sprintf('%s.%s', get_called_class(), $key);
    }

    public static function configure($key, $value)
    {
        Config::set(static::getClassKey($key), $value);
    }

    protected function getConfigValue($key, $default = null)
    {
        return Config::get(static::getClassKey($key), $default);
    }
} 
