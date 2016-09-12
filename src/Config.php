<?php
namespace Robo;

class Config
{
    const PROGRESS_BAR_AUTO_DISPLAY_INTERVAL = 'progress-delay';
    const DEFAULT_PROGRESS_DELAY = 2;
    const SIMULATE = 'simulate';
    const SUPRESS_MESSAGES = 'supress-messages';
    const DECORATED = 'decorated';

    protected $config = [];

    /**
     * Fet a configuration value
     * @param string $key Which config item to look up
     * @param string|null $defaultOverride Override usual default value with a different default
     * @return mixed
     */
    public function get($key, $defaultOverride = null)
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }
        return $this->getDefault($key, $defaultOverride);
    }

    /**
     * Set a configu value
     * @param string $key
     * @param mixed $value
     * @return Config
     */
    public function set($key, $value)
    {
        $this->config[$key] = $value;
        return $this;
    }

    /**
     * Return an associative array containing all of the global configuration
     * options and their default values.
     * @return array
     */
    public function getGlobalOptionDefaultValues()
    {
        $globalOptions =
        [
            self::PROGRESS_BAR_AUTO_DISPLAY_INTERVAL => self::DEFAULT_PROGRESS_DELAY,
            self::SIMULATE => false,
            self::SUPRESS_MESSAGES => false,
        ];

        return $globalOptions;
    }

    /**
     * Return the default value for a given configuration item.
     * @param string $key
     * @param string|null $defaultOverride
     * @return mixed
     */
    public function getDefault($key, $defaultOverride = null)
    {
        $globalOptions = $this->getGlobalOptionDefaultValues();
        return isset($globalOptions[$key]) ? $globalOptions[$key] : $defaultOverride;
    }

    public function isSimulated()
    {
        return $this->get(self::SIMULATE);
    }

    public function setSimulated($simulated = true)
    {
        return $this->set(self::SIMULATE, $simulated);
    }

    public function isDecorated()
    {
        return $this->get(self::DECORATED);
    }

    public function setDecorated($decorated = true)
    {
        return $this->set(self::DECORATED, $decorated);
    }

    public function isSupressed()
    {
        return $this->get(self::SUPRESS_MESSAGES);
    }

    public function setSupressed($supressed = true)
    {
        return $this->set(self::SUPRESS_MESSAGES, $supressed);
    }

    public function setProgressBarAutoDisplayInterval($interval)
    {
        return $this->set(self::PROGRESS_BAR_AUTO_DISPLAY_INTERVAL, $interval);
    }
}
