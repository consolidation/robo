<?php

/*
 * This file is derived from part of the Symfony package, which is
 * (c) Fabien Potencier <fabien@symfony.com>
 */

namespace Robo\Common;

use Symfony\Component\Process\Exception\InvalidArgumentException;

/**
 * ProcessUtils is a bunch of utility methods. We want to allow Robo 1.x
 * to work with Symfony 4.x while remaining backwards compatibility. This
 * requires us to replace some deprecated functionality removed in Symfony.
 */
class ProcessUtils
{
    /**
     * This class should not be instantiated.
     */
    private function __construct()
    {
    }

    /**
     * Escapes a string to be used as a shell argument.
     *
     * @param string $argument
     *   The argument that will be escaped.
     *
     * @return string
     *   The escaped argument.
     *
     * @deprecated since version 3.3, to be removed in 4.0. Use a command line array or give env vars to the `Process::start/run()` method instead.
     */
    public static function escapeArgument($argument)
    {
        @trigger_error('The ' . __METHOD__ . '() method is a copy of a method that was deprecated by Symfony 3.3 and removed in Symfony 4; it will be removed in Robo 2.0.', E_USER_DEPRECATED);

        //Fix for PHP bug #43784 escapeshellarg removes % from given string
        //Fix for PHP bug #49446 escapeshellarg doesn't work on Windows
        //@see https://bugs.php.net/bug.php?id=43784
        //@see https://bugs.php.net/bug.php?id=49446
        if ('\\' === DIRECTORY_SEPARATOR) {
            if ('' === $argument) {
                return escapeshellarg($argument);
            }

            $escapedArgument = '';
            $quote = false;
            foreach (preg_split('/(")/', $argument, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE) as $part) {
                if ('"' === $part) {
                    $escapedArgument .= '\\"';
                } elseif (self::isSurroundedBy($part, '%')) {
                    // Avoid environment variable expansion
                    $escapedArgument .= '^%"' . substr($part, 1, -1) . '"^%';
                } else {
                    // escape trailing backslash
                    if ('\\' === substr($part, -1)) {
                        $part .= '\\';
                    }
                    $quote = true;
                    $escapedArgument .= $part;
                }
            }
            if ($quote) {
                $escapedArgument = '"' . $escapedArgument . '"';
            }

            return $escapedArgument;
        }

        return "'" . str_replace("'", "'\\''", $argument) . "'";
    }

    private static function isSurroundedBy($arg, $char)
    {
        return 2 < strlen($arg) && $char === $arg[0] && $char === $arg[strlen($arg) - 1];
    }
}
