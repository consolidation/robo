<?php
namespace Robo\Config;

use Dflydev\DotAccessData\Data;

class Config
{
    const PROGRESS_BAR_AUTO_DISPLAY_INTERVAL = 'progress-delay';
    const DEFAULT_PROGRESS_DELAY = 2;
    const SIMULATE = 'simulate';
    const DECORATED = 'decorated';

    /**
     * @var Data
     */
    protected $config;

    /**
     * Create a new configuration object, and initialize it with
     * the provided nested array containing configuration data.
     */
    public function __construct(array $data = null)
    {
        $this->config = new Data($data);
    }

    /**
     * Determine if a non-default config value exists.
     */
    public function has($key)
    {
        return ($this->config->has($key));
    }

    /**
     * Fetch a configuration value
     *
     * @param string $key Which config item to look up
     * @param string|null $defaultOverride Override usual default value with a different default. Deprecated; provide defaults to the config processor instead.
     *
     * @return mixed
     */
    public function get($key, $defaultOverride = null)
    {
        if ($this->has($key)) {
            return $this->config->get($key);
        }
        return $this->getDefault($key, $defaultOverride);
    }

    /**
     * Set a config value
     *
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        $this->config->set($key, $value);
        return $this;
    }

    /**
     * Import configuration from the provided nexted array, replacing whatever
     * was here previously.
     *
     * @param array|ConfigLoaderInterface $data
     * @return Config
     */
    public function import($data)
    {
        if (!empty($data)) {
            $this->config->import($data, true);
        }
        return $this;
    }

    /**
     * Extend this configuration by merging the provided nested array.
     *
     * @param array|ConfigLoaderInterface $data
     */
    public function extend($data)
    {
        $data = array_merge_recursive($this->config->export(), $data);
        return $this->import($data);
    }

    /**
     * Export all configuration as a nested array.
     */
    public function export()
    {
        return $this->config->export();
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
     * Return the default value for a given configuration item.
     *
     * @param string $key
     * @param mixed $defaultOverride
     *
     * @return mixed
     */
    public function getDefault($key, $defaultOverride = null)
    {
        $globalOptions = $this->getGlobalOptionDefaultValues();
        return isset($globalOptions[$key]) ? $globalOptions[$key] : $defaultOverride;
    }

    /**
     * @return bool
     */
    public function isSimulated()
    {
        return $this->get(self::SIMULATE);
    }

    /**
     * @param bool $simulated
     *
     * @return $this
     */
    public function setSimulated($simulated = true)
    {
        return $this->set(self::SIMULATE, $simulated);
    }

    /**
     * @return bool
     */
    public function isDecorated()
    {
        return $this->get(self::DECORATED);
    }

    /**
     * @param bool $decorated
     *
     * @return $this
     */
    public function setDecorated($decorated = true)
    {
        return $this->set(self::DECORATED, $decorated);
    }

    /**
     * @param int $interval
     *
     * @return $this
     */
    public function setProgressBarAutoDisplayInterval($interval)
    {
        return $this->set(self::PROGRESS_BAR_AUTO_DISPLAY_INTERVAL, $interval);
    }
}
