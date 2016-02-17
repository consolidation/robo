<?php
namespace Robo\Common;

/**
 * Additional log levels for use in Console applications.
 *
 * ConsoleLogLevels may be used by methods of Symfony Command
 * in applications where it is known that the StyledConsoleLogger
 * is in use.  These log levels provide access to the 'success'
 * styled output method.  Code in reusable libraries that may
 * be used with a standard Psr-3 logger should aviod using these
 * log levels.
 *
 * @author Greg Anderson <greg.1.anderson@greenknowe.org>
 */
class ConsoleLogLevel extends \Psr\Log\LogLevel
{
    /**
     * Command successfully completed some operation.
     * Displayed at VERBOSITY_NORMAL.
     */
    const SUCCESS = 'success';
}
