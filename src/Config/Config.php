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
     * Fetch an option value from a given key, or, if that specific key does
     * not contain a value, then consult various fallback options until a
     * value is found.
     *
     * Given the following inputs:
     *   - $prefix  = "command."
     *   - $group   = "foo.bar.baz"
     *   - $postfix = ".options."
     * This method will then consider, in order:
     *   - command.foo.bar.baz.options
     *   - command.foo.bar.options
     *   - command.foo.options
     * If any of these contain an option for "$key", then return its value.
     */
    public function getWithFallback($key, $group, $prefix = '', $postfix = '.')
    {
        $configKey = "{$prefix}{$group}${postfix}{$key}";
        if ($this->has($configKey)) {
            return $this->get($configKey);
        }
        if ($this->hasDefault($configKey)) {
            return $this->getDefault($configKey);
        }
        $moreGeneralGroupname = preg_replace('#\.[^.]*$#', '', $group);
        if ($moreGeneralGroupname != $group) {
            return $this->getWithFallback($key, $moreGeneralGroupname, $prefix, $postfix);
        }
        return null;
    }

    /**
     * Works like 'getWithFallback', but merges results from all applicable
     * groups. Settings from most specific group take precedence.
     */
    public function getWithMerge($key, $group, $prefix = '', $postfix = '.')
    {
        $configKey = "{$prefix}{$group}${postfix}{$key}";
        $result = [];
        if ($this->has($configKey)) {
            $result = $this->get($configKey);
        } elseif ($this->hasDefault($configKey)) {
            $result = $this->getDefault($configKey);
        }
        if (!is_array($result)) {
            throw new \UnexpectedValueException($configKey . ' must be a list of settings to apply.');
        }
        $moreGeneralGroupname = preg_replace('#\.[^.]*$#', '', $group);
        if ($moreGeneralGroupname != $group) {
            $result += $this->getWithMerge($key, $moreGeneralGroupname, $prefix, $postfix);
        }
        return $result;
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
     * that the method is valid, but it would catch almost every misuse.
     */
    public function applyConfiguration($object, $configurationKey, $group = '', $prefix = '', $postfix = '')
    {
        if (!empty($group) && empty($postfix)) {
            $postfix = '.';
        }
        $settings = $this->getWithMerge($configurationKey, $group, $prefix, $postfix);
        foreach ($settings as $setterMethod => $args) {
            // TODO: Should it be possible to make $args a nested array
            // to make this code call the setter method multiple times?
            $fn = [$object, $setterMethod];
            if (is_callable($fn)) {
                call_user_func_array($fn, (array)$args);
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
     *
     * @return mixed
     */
    public function hasDefault($key)
    {
        return isset($this->defaults[$key]);
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
        return $this->hasDefault($key) ? $this->defaults[$key] : $defaultOverride;
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
