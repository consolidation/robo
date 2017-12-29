<?php

/**
 * @file
 * Contains \Robo\composer\ScriptHandler.
 */

namespace Robo\composer;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;

class ScriptHandler
{

    /**
     * Run prior to `composer installl` when a composer.lock is present.
     * @param Event $event
     */
    public static function checkDependencies(Event $event)
    {
        if (version_compare(PHP_VERSION, '5.6.0') < 0) {
            static::checkDependenciesFor55();
        }
    }

    /**
     * Check to see if the dependencies in composer.lock are compatible
     * with php 5.5.
     */
    protected static function checkDependenciesFor55()
    {
        $fs = new Filesystem();
        if (!$fs->exists('composer.lock')) {
            return;
        }

        $composerLockContents = file_get_contents('composer.lock');
        if (preg_match('#"php":.*(5\.6)#', $composerLockContents)) {
            static::fixDependenciesFor55();
        }
    }

    protected static function fixDependenciesFor55()
    {
        $fs = new Filesystem();
        $status = 0;

        $fs->remove('composer.lock');

        // Composer has already read our composer.json file, so we will
        // need to run in a new process to fix things up.
        passthru('composer install --ansi', $status);

        // Don't continue with the initial 'composer install' command
        exit($status);
    }
}
