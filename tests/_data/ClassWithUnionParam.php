<?php

use Robo\Result;

/**
 * A test file. Used for testing documentation generation.
 */
class ClassWithUnionParam
{
    final public static function executeTask(Robo\Task\Composer\Install|Robo\Task\Composer\Update $task): string|array
    {
        return [];
    }
}
