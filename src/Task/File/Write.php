<?php

namespace Robo\Task\File;

use Robo\Result;
use Robo\Task\BaseTask;

/**
 * Writes to file.
 *
 * ``` php
 * <?php
 * $this->taskWriteToFile('blogpost.md')
 *      ->line('-----')
 *      ->line(date('Y-m-d').' '.$title)
 *      ->line('----')
 *      ->run();
 * ?>
 * ```
 *
 * @method append()
 */
class Write extends BaseTask
{
    use \Robo\Common\DynamicParams;

    protected $filename;
    protected $append = false;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * add a line.
     *
     * @param string $line
     *
     * @return Write The current instance
     */
    public function line($line)
    {
        $this->text($line . "\n");
        return $this;
    }

    /**
     * add more lines.
     *
     * @param array $lines
     *
     * @return Write The current instance
     */
    public function lines(array $lines)
    {
        $this->text(implode("\n", $lines) . "\n");
        return $this;
    }

    /**
     * add a text.
     *
     * @param string $text
     *
     * @return Write The current instance
     */
    public function text($text)
    {
        $this->stack[] = array_merge([__FUNCTION__ . 'Collect'], func_get_args());
        return $this;
    }

    /**
     * add a text from a file.
     *
     * Note that the file is read in the run() method of this task.
     * To load text from the current state of a file (e.g. one that may
     * be deleted or altered by other tasks prior the execution of this one),
     * use:
     *       $task->text(file_get_contents($filename));
     *
     * @param string $filename
     *
     * @return Write The current instance
     */
    public function textFromFile($filename)
    {
        $this->stack[] = array_merge([__FUNCTION__ . 'Collect'], func_get_args());
        return $this;
    }

    /**
     * substitute a placeholder with value, placeholder must be enclosed by `{}`.
     *
     * @param string $name
     * @param string $val
     *
     * @return Write The current instance
     */
    public function place($name, $val)
    {
        $this->replace('{'.$name.'}', $val);

        return $this;
    }

    /**
     * replace any string with value.
     *
     * @param string $string
     * @param string $replacement
     *
     * @return Write The current instance
     */
    public function replace($string, $replacement)
    {
        $this->stack[] = array_merge([__FUNCTION__ . 'Collect'], func_get_args());
        return $this;
    }

    /**
     * replace any string with value using regular expression.
     *
     * @param string $pattern
     * @param string $replacement
     *
     * @return Write The current instance
     */
    public function regexReplace($pattern, $replacement)
    {
        $this->stack[] = array_merge([__FUNCTION__ . 'Collect'], func_get_args());
        return $this;
    }

    protected function textFromFileCollect($contents, $filename)
    {
        return $contents . file_get_contents($filename);
    }

    protected function replaceCollect($contents, $string, $replacement)
    {
        return str_replace($string, $replacement, $contents);
    }

    protected function regexReplaceCollect($contents, $pattern, $replacement)
    {
        return preg_replace($pattern, $replacement, $contents);
    }

    protected function textCollect($contents, $text)
    {
        return $contents . $text;
    }

    protected function getContents()
    {
        $contents = "";
        if ($this->append) {
            $contents = file_get_contents($this->filename);
        }
        foreach ($this->stack as $action) {
            $command = array_shift($action);
            if (method_exists($this, $command)) {
                array_unshift($action, $contents);
                $contents = call_user_func_array([$this, $command], $action);
            }
        }
        return $contents;
    }

    public function run()
    {
        $this->printTaskInfo("Writing to <info>{$this->filename}</info>.");
        $contents = $this->getContents();
        if (!file_exists(dirname($this->filename))) {
            mkdir(dirname($this->filename), 0777, true);
        }
        $res = file_put_contents($this->filename, $contents);
        if ($res === false) {
            return Result::error($this, "File {$this->filename} couldn't be created");
        }

        return Result::success($this);
    }

    public function getPath()
    {
        return $this->filename;
    }
}
