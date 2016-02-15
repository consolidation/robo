<?php
namespace Robo\Common;

/**
 * Additional log levels for use in Console applications.
 *
 * ConsoleLogLevels may be used by methods of Symfony Command
 * in applications where it is known that the StyledConsoleLogger
 * is in use.  These log levels provide access to the 'success'
 * styled output method.
 *
 * @author Greg Anderson <greg.1.anderson@greenknowe.org>
 */
class ConsoleLogLevel extends \Psr\Log\LogLevel
{
    // Notify the user that forward progress was made on the command.
    // Uses the 'success' styled output method.  Displayed at
    // VERBOSITY_NORMAL.  Use LogLevel::INFO for messages that should
    // only appear at VERBOSITY_VERBOSE.
    const OK = 'ok';

    // Means the command was successful. Should appear at most once
    // per command, although may occasionally appear multiple times
    // if subcommands are executed.  Displayed at VERBOSITY_NORMAL.
    const SUCCESS = 'success';
}
