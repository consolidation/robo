<?php

namespace Robo\Common;

use Symfony\Component\Yaml\Yaml;
use Grasmash\YamlExpander\Expander;

/**
 * Load configuration files, and fill in any property values that
 * need to be expanded.
 */
class ConfigLoader
{
    protected $config = [];

    public function load($path)
    {
        // We silently skip any nonexistent config files, so that
        // clients may simply `load` all of their candidates.
        if (!file_exists($path)) {
            return;
        }
        $this->add(Yaml::parse(file_get_contents($path)));
    }

    public function add($data)
    {
        $this->config = Expander::expandArrayProperties($data, $this->config);
    }

    public function export()
    {
        return $this->config;
    }
}
