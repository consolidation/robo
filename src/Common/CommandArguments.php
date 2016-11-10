<?php
namespace Robo\Common;

use Symfony\Component\Process\ProcessUtils;

/**
 * Use this to add arguments and options to the $arguments property.
 */
trait CommandArguments
{
    /**
     * @var string
     */
    protected $arguments = '';

    /**
     * Pass argument to executable. Its value will be automatically escaped.
     *
     * @param string $arg
     *
     * @return $this
     */
    public function arg($arg)
    {
        return $this->args($arg);
    }

    /**
     * Pass methods parameters as arguments to executable. Argument values
     * are automatically escaped.
     *
     * @param string|string[] $args
     *
     * @return $this
     */
    public function args($args)
    {
        if (!is_array($args)) {
            $args = func_get_args();
        }
        $this->arguments .= ' ' . implode(' ', array_map('static::escape', $args));
        return $this;
    }

    /**
     * Pass the provided string in its raw (as provided) form as an argument to executable.
     *
     * @param string $arg
     */
    public function rawArg($arg)
    {
        $this->arguments .= " $arg";
    }

    /**
     * Escape the provided value, unless it contains only alphanumeric
     * plus a few other basic characters.
     *
     * @param string $value
     *
     * @return string
     */
    public static function escape($value)
    {
        if (preg_match('/^[a-zA-Z0-9\/\.@~_-]+$/', $value)) {
            return $value;
        }
        return ProcessUtils::escapeArgument($value);
    }

    /**
     * Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
     * Option values are automatically escaped.
     *
     * @param string $option
     * @param string $value
     *
     * @return $this
     */
    public function option($option, $value = null)
    {
        if ($option !== null and strpos($option, '-') !== 0) {
            $option = "--$option";
        }
        $this->arguments .= null == $option ? '' : " " . $option;
        $this->arguments .= null == $value ? '' : " " . static::escape($value);
        return $this;
    }

    /**
     * Pass multiple options to executable. Value can be a string or array.
     * Option values are automatically escaped.
     *
     * @param string $option
     * @param string|array $value
     *
     * @return $this
     */
    public function optionList($option, $value = array())
    {
        if (is_array($value)) {
            foreach ($value as $item) {
                $this->optionList($option, $item);
            }
        } else {
            $this->option($option, $value);
        }

        return $this;
    }
}
