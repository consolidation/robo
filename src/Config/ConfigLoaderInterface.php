<?php

namespace Robo\Config;

/**
 * Load configuration files, and fill in any property values that
 * need to be expanded.
 */
interface ConfigLoaderInterface
{
    public function add($data);
    public function export();
}
