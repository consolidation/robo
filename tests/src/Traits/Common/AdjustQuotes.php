<?php

namespace Robo\Traits\Common;

trait AdjustQuotes
{
    protected function adjustQuotes($expected)
    {
        $isWindows = defined('PHP_WINDOWS_VERSION_MAJOR');

        if ($isWindows) {
            return strtr($expected, "'", '"');
        }
        return $expected;
    }
}
