<?php
namespace Robo\Task\File;

use Robo\Result;
use Robo\Task\BaseTask;

/**
 * Writes to file
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
     * add a line
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
     * add more lines
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
     * add a text
     *
     * @param string $text
     *
     * @return Write The current instance
     */
    public function text($text)
    {
        $this->stack[] = array_merge(['_' . __FUNCTION__], func_get_args());
        return $this;
    }

    /**
     * add a text from a file
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
        $this->stack[] = array_merge(['_' . __FUNCTION__], func_get_args());
        return $this;
    }

    /**
     * substitute a placeholder with value, placeholder must be enclosed by `{}`
     *
     * @param string $name
     * @param string $val
     *
     * @return Write The current instance
     */
    public function place($name, $val)
    {
        $this->replace('{' . $name . '}', $val);
        return $this;
    }

    /**
     * replace any string with value
     *
     * @param string $string
     * @param string $replacement
     *
     * @return Write The current instance
     */
    public function replace($string, $replacement)
    {
        $this->stack[] = array_merge(['_' . __FUNCTION__], func_get_args());
        return $this;
    }

    /**
     * replace any string with value using regular expression
     *
     * @param string $pattern
     * @param string $replacement
     *
     * @return Write The current instance
     */
    public function regexReplace($pattern, $replacement)
    {
        $this->stack[] = array_merge(['_' . __FUNCTION__], func_get_args());
        return $this;
    }

    protected function _textFromFile($body, $filename) {
        return $body . file_get_contents($filename);
    }

    protected function _replace($body, $string, $replacement) {
        return str_replace($string, $replacement, $body);
    }

    protected function _regexReplace($body, $pattern, $replacement) {
        return preg_replace($pattern, $replacement, $body);
    }

    protected function _text($body, $text) {
        return $body . $text;
    }

    protected function getContents() {
        $body = "";
        if ($this->append) {
            $body = file_get_contents($this->filename);
        }
        foreach ($this->stack as $action) {
            $command = array_shift($action);
            if (method_exists($this, $command)) {
                array_unshift($action, $body);
                $body = call_user_func_array([$this, $command], $action);
            }
        }
        return $body;
    }

    public function run()
    {
        $this->printTaskInfo("Writing to <info>{$this->filename}</info>.");
        $body = $this->getContents();
        if (!file_exists(dirname($this->filename))) {
            mkdir(dirname($this->filename), 0777, true);
        }
        $res = file_put_contents($this->filename, $body);
        if ($res === false) {
            return Result::error($this, "File {$this->filename} couldn't be created");
        }
        return Result::success($this);
    }
}
