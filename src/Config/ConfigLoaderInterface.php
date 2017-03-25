<?php

namespace Robo\Config;

/**
 * Load configuration files, and fill in any property values that
 * need to be expanded.
 */
interface ConfigLoaderInterface
{
    public function import($data);
    public function add($data);
    public function export();
    public function keys();
    public function getSourceName();
    public function setSourceName($source);
}
