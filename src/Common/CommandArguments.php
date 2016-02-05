<?php
namespace Robo\Common;

/**
 * Use this to add arguments and options to the $arguments property.
 */
trait CommandArguments
{
    protected $arguments = '';

    /**
     * Pass argument to executable
     *
     * @param $arg
     * @return $this
     */
    public function arg($arg)
    {
        return $this->args($arg);
    }

    /**
     * Pass methods parameters as arguments to executable
     *
     * @param $args
     * @return $this
     */
    public function args($args)
    {
        if (!is_array($args)) {
            $args = func_get_args();
        }
        $this->arguments .= " ".implode(' ', $args);
        return $this;
    }

    /**
     * Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
     *
     * Option values must be explicitly escaped with escapeshellarg if necessary before being passed to
     * this function.
     *
     * @param $option
     * @param string $value
     * @return $this
     */
    public function option($option, $value = null)
    {
        if ($option !== null and strpos($option, '-') !== 0) {
            $option = "--$option";
        }
        $this->arguments .= null == $option ? '' : " " . $option;
        $this->arguments .= null == $value ? '' : " " . $value;
        return $this;
    }

    /**
     * Pass multiple options to executable. Value can be a string or array.
     *
     * Option values should be provided in raw, unescaped form; they are always
     * escaped via escapeshellarg in this function.
     *
     * @param $option
     * @param string|array $value
     * @return $this
     */
    public function optionList($option, $value = array())
    {
        if (is_array($value)) {
            foreach ($value as $item) {
                $this->optionList($option, $item);
            }
        }
        else {
            $this->option($option, escapeshellarg($value));
        }

        return $this;
    }

}
