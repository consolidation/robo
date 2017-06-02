<?php
namespace Robo\Config;

use Dflydev\DotAccessData\Data;

class Config
{
    const PROGRESS_BAR_AUTO_DISPLAY_INTERVAL = 'progress-delay';
    const DEFAULT_PROGRESS_DELAY = 2;
    const SIMULATE = 'simulate';
    const INTERACTIVE = 'interactive';
    const DECORATED = 'decorated';

    /**
     * @var Data
     */
    protected $config;

    /**
     * @var array
     */
    protected $defaults;

    /**
     * Create a new configuration object, and initialize it with
     * the provided nested array containing configuration data.
     */
    public function __construct(array $data = null)
    {
        $this->config = new Data($data);
        $this->defaults = $this->getGlobalOptionDefaultValues();
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
     * was here previously. No processing is done on the provided data.
     *
     * @param array $data
     * @return Config
     */
    public function import($data)
    {
        $this->config = new Data($data);
        if (!empty($data)) {
            $this->config->import($data, true);
        }
        return $this;
    }

    /**
     * Export all configuration as a nested array.
     */
    public function export()
    {
        return $this->config->export();
    }

    /**
     * Given an object that contains configuration methods, inject any
     * configuration found in the configuration file.
     *
     * The proper use for this method is to call setter methods of the
     * provided object. Using configuration to call methods that do work
     * is an abuse of this mechanism.
     *
     * TODO: We could use reflection to test to see if the return type
     * of the provided object is a reference to the object itself. All
     * setter methods should do this. This test is insufficient to guarentee
     * that the method is valid, but it would be a good start.
     */
    public function applyConfiguration($object, $configurationKey)
    {
        if ($this->has($configurationKey)) {
            $settings = $this->get($configurationKey);
            foreach ($settings as $setterMethod => $args) {
                // TODO: Should it be possible to make $args a nested array
                // to make this code call the setter method multiple times?
                call_user_func_array([$object, $setterMethod], (array)$args);
            }
        }
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
        return isset($this->defaults[$key]) ? $this->defaults[$key] : $defaultOverride;
    }

    /**
     * Set the default value for a configuration setting. This allows us to
     * set defaults either before or after more specific configuration values
     * are loaded. Keeping defaults separate from current settings also
     * allows us to determine when a setting has been overridden.
     *
     * @param string $key
     * @param string $value
     */
    public function setDefault($key, $value)
    {
        $this->defaults[$key] = $value;
        return $this;
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
    public function isInteractive()
    {
        return $this->get(self::INTERACTIVE);
    }

    /**
     * @param bool $simulated
     *
     * @return $this
     */
    public function setInteractive($interactive = true)
    {
        return $this->set(self::INTERACTIVE, $interactive);
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
