<?php

namespace Robo\Config;

use Symfony\Component\Yaml\Yaml;

/**
 * Load configuration files, and fill in any property values that
 * need to be expanded.
 */
class YamlConfigLoader extends ConfigLoader
{
    protected $config = [];

    public function load($path)
    {
        $this->setSourceName($path);

        // We silently skip any nonexistent config files, so that
        // clients may simply `load` all of their candidates.
        if (!file_exists($path)) {
            return $this;
        }
        return $this->import(Yaml::parse(file_get_contents($path)));
    }
}
