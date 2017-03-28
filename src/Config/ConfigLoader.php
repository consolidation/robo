<?php

namespace Robo\Config;

use Grasmash\YamlExpander\Expander;

/**
 * Load configuration files, and fill in any property values that
 * need to be expanded.
 */
class ConfigLoader implements ConfigLoaderInterface
{
    protected $config = [];
    protected $source = '';

    public function getSourceName()
    {
        return $this->source;
    }

    protected function setSourceName($source)
    {
        $this->source = $source;
        return $this;
    }

    public function export()
    {
        return $this->config;
    }

    public function keys()
    {
        return array_keys($this->config);
    }
}
