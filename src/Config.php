<?php
namespace Robo;

class Config
{
    const SIMULATE = 'simulate';
    const PROGRESS_BAR_AUTO_DISPLAY_INTERVAL = 'progress-delay';

    protected $config = [];

    public function get($key, $default = null)
    {
        return isset($this->config[$key]) ? $this->config[$key] : $default;
    }

    public function set($key, $value)
    {
        $this->config[$key] = $value;
    }

    public function setGlobalOptions($input)
    {
        $globalOptions =
        [
            self::SIMULATE => false,
            self::PROGRESS_BAR_AUTO_DISPLAY_INTERVAL => 2,
        ];

        foreach ($globalOptions as $option => $default) {
            $value = $input->hasOption($option) ? $input->getOption($option) : null;
            // Unfortunately, the `?:` operator does not differentate between `0` and `null`
            if (!isset($value)) {
                $value = $default;
            }
            $this->set($option, $value);
        }
    }

    public function isSimulated()
    {
        return $this->get(self::SIMULATE);
    }
}

