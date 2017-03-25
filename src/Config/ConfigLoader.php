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

    public function setSourceName($source)
    {
        $this->source = $source;
    }

    public function add($data)
    {
        if ($data instanceof ConfigLoaderInterface) {
            $data = $data->export();
        }
        $this->config = Expander::expandArrayProperties($data, $this->config);
    }

    public function export()
    {
        return $this->config;
    }
}
