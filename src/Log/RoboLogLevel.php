<?php

namespace Robo\Log;

class RoboLogLevel extends \Psr\Log\LogLevel
{
    /**
     * Command did something in simulated mode.
     * Displayed at VERBOSITY_NORMAL.
     */
    public const SIMULATED_ACTION = 'simulated';

    /**
     * Successful action; equivalent to 'NOTICE'
     */
    public const SUCCESS = 'success';
}
