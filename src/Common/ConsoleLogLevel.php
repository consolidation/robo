<?php
namespace Robo\Common;

/**
 * Additional log levels for use in Console applications.
 */
class ConsoleLogLevel extends \Psr\Log\LogLevel
{
    // Various 'success' messages.  Like 'notice'
    const OK = 'ok';

    // Means the command was successful. Should appear at most once
    // per command (perhaps more if subcommands are executed, though).
    // Like 'notice'.
    const SUCCESS = 'success';
}
