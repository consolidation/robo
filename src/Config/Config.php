<?php
namespace Robo\Config;

class Config extends \Consolidation\Config\Config implements GlobalOptionDefaultValuesInterface
{
    const PROGRESS_BAR_AUTO_DISPLAY_INTERVAL = 'progress-delay';
    const DEFAULT_PROGRESS_DELAY = 2;
    const SIMULATE = 'simulate';

    // Read-only configuration properties; changing these has no effect.
    const INTERACTIVE = 'interactive';
    const DECORATED = 'decorated';

    /**
     * Create a new configuration object, and initialize it with
     * the provided nested array containing configuration data.
     */
    public function __construct(array $data = null)
    {
        parent::__construct($data);
        $this->defaults = $this->getGlobalOptionDefaultValues();
    }

    /**
     * Return an associative array containing all of the global configuration
     * options and their default values.
     *
     * @return array
     */
    public function getGlobalOptionDefaultValues()
    {
        $globalOptions =
        [
            self::PROGRESS_BAR_AUTO_DISPLAY_INTERVAL => self::DEFAULT_PROGRESS_DELAY,
            self::SIMULATE => false,
        ];

        return $globalOptions;
    }

    /**
     * @deprecated Use $config->get(Config::SIMULATE)
     *
     * @return bool
     */
    public function isSimulated()
    {
        return $this->get(self::SIMULATE);
    }

    /**
     * @deprecated Use $config->set(Config::SIMULATE, true)
     *
     * @param bool $simulated
     *
     * @return $this
     */
    public function setSimulated($simulated = true)
    {
        return $this->set(self::SIMULATE, $simulated);
    }

    /**
     * @deprecated Use $config->get(Config::INTERACTIVE)
     *
     * @return bool
     */
    public function isInteractive()
    {
        return $this->get(self::INTERACTIVE);
    }

    /**
     * @deprecated Use $config->set(Config::INTERACTIVE, true)
     *
     * @param bool $interactive
     *
     * @return $this
     */
    public function setInteractive($interactive = true)
    {
        return $this->set(self::INTERACTIVE, $interactive);
    }

    /**
     * @deprecated Use $config->get(Config::DECORATED)
     *
     * @return bool
     */
    public function isDecorated()
    {
        return $this->get(self::DECORATED);
    }

    /**
     * @deprecated Use $config->set(Config::DECORATED, true)
     *
     * @param bool $decorated
     *
     * @return $this
     */
    public function setDecorated($decorated = true)
    {
        return $this->set(self::DECORATED, $decorated);
    }

    /**
     * @deprecated Use $config->set(Config::PROGRESS_BAR_AUTO_DISPLAY_INTERVAL, $interval)
     *
     * @param int $interval
     *
     * @return $this
     */
    public function setProgressBarAutoDisplayInterval($interval)
    {
        return $this->set(self::PROGRESS_BAR_AUTO_DISPLAY_INTERVAL, $interval);
    }
}
