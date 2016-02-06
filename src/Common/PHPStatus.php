<?php

namespace Robo\Common;

use Robo\Result;

trait PHPStatus
{
    /**
     * Check for availablilty of PHP extensions.
     */
    protected function checkExtension($service, $extensionList)
    {
        foreach ((array) $extensionList as $ext) {
            if (!extension_loaded($ext)) {
                return Result::error($this, "You must use PHP with the $ext extension enabled to use $service");
            }
        }

        return Result::success($this);
    }
}
