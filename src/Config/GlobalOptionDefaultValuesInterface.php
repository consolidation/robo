<?php
namespace Robo\Config;

/**
 * @deprecated Use robo.yml instead
 *
 * robo.yml:
 *
 * options:
 *   simulated: false
 *   progress-delay: 2
 *
 * etc.
 */
interface GlobalOptionDefaultValuesInterface
{
    /**
     * Return an associative array of option-key => default-value
     */
    public function getGlobalOptionDefaultValues();
}
