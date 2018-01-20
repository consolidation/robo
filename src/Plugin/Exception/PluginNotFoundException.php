<?php

namespace Robo\Plugin\Exception;

/**
 * Plugin exception class to be thrown when a plugin ID could not be found.
 */
class PluginNotFoundException extends PluginException
{
    /**
     * PluginNotFoundException constructor.
     *
     * @param $pluginId
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($pluginId, $message = '', $code = 0, \Exception $previous = null)
    {
        if (empty($message)) {
            $message = sprintf("Plugin ID '%s' was not found.", $pluginId);
        }
        parent::__construct($message, $code, $previous);
    }
}
