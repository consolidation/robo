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
    protected $body = "";
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
        $this->body .= $line . "\n";
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
        $this->body .= implode("\n", $lines) . "\n";
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
        $this->body .= $text;
        return $this;
    }

    /**
     * add a text from a file
     *
     * @param string $filename
     *
     * @return Write The current instance
     */
    public function textFromFile($filename)
    {
        $this->text(file_get_contents($filename));
        return $this;
    }

    /**
     * substitute a placeholder with value, placeholder must be enclosed by {{}}
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
        $this->body = str_replace($string, $replacement, $this->body);
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
        $this->body = preg_replace($pattern, $replacement, $this->body);
        return $this;
    }

    public function run()
    {
        $this->printTaskInfo("Writing to <info>{$this->filename}</info>.");
        if ($this->append) {
            $this->body = file_get_contents($this->filename) . $this->body;
        }
        if (!file_exists(dirname($this->filename))) {
            mkdir(dirname($this->filename), 0777, true);
        }
        $res = file_put_contents($this->filename, $this->body);
        if ($res === false) {
            return Result::error($this, "File {$this->filename} couldn't be created");
        }
        return Result::success($this);
    }
}
