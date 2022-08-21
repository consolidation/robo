<?php

use Robo\Result;

/**
 * A test file. Used for testing documentation generation.
 */
class ClassWithUnionParam
{
    /**
     * Short description
     *
     * Long description 1
     * Long description 2
     * Long description 3
     *
     * @author Gintautas Miselis <gintautas@localhost>
     * @since 2.0.0 New method
     */
    final public static function executeTask(Robo\Task\Composer\Install|Robo\Task\Composer\Update $task): string|array
    {
        return [];
    }
}
