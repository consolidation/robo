<?php
namespace Robo\Task\FileSystem;

use Robo\Result;
use Robo\Output;
use Robo\Common\DynamicConfig;
use Robo\Contract\TaskInterface;

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
class WriteToFile implements TaskInterface
{
    use Output;
    use \Robo\Common\DynamicConfig;

    protected $filename;
    protected $body = "";
    protected $append = false;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public static function init($filename)
    {
        return new self($filename);
    }

    public function line($line)
    {
        $this->body .= $line . "\n";
        return $this;
    }

    public function lines($lines)
    {
        $this->body .= implode("\n", $lines) . "\n";
        return $this;
    }

    public function text($text)
    {
        $this->body .= $text;
        return $this;
    }

    public function textFromFile($filename)
    {
        $this->text(file_get_contents($filename));
        return $this;
    }

    public function place($name, $val)
    {
        $this->body = str_replace("{{$name}}", $val, $this->body);
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