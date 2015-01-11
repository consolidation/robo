<?php
namespace Robo\Task\FileSystem;

use Robo\Result;
use Robo\Output;
use Robo\Common\DynamicConfig;
use Robo\Contract\TaskInterface;

/**
 * Performs search and replace inside a files.
 *
 * ``` php
 * <?php
 * $this->taskReplaceInFile('VERSION')
 *  ->from('0.2.0')
 *  ->to('0.3.0')
 *  ->run();
 *
 * $this->taskReplaceInFile('README.md')
 *  ->from(date('Y')-1)
 *  ->to(date('Y'))
 *  ->run();
 *
 * $this->taskReplaceInFile('config.yml')
 *  ->regex('~^service:~')
 *  ->to('services:')
 *  ->run();
 * ?>
 * ```
 *
 * @method regex(string)
 * @method from(string)
 * @method to(string)
 */
class ReplaceInFile implements TaskInterface
{
    use Output;
    use \Robo\Common\DynamicConfig;

    protected $filename;
    protected $from;
    protected $to;
    protected $regex;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public static function init($filename)
    {
        return new self($filename);
    }

    function run()
    {
        if (!file_exists($this->filename)) {
            $this->printTaskInfo("<error>File {$this->filename} does not exist</error>");
            return false;
        }

        $text = file_get_contents($this->filename);
        if ($this->regex) {
            $text = preg_replace($this->regex, $this->to, $text, -1, $count);
        } else {
            $text = str_replace($this->from, $this->to, $text, $count);
        }
        $res = file_put_contents($this->filename, $text);
        if ($res === false) {
            return Result::error($this, "Error writing to file {$this->filename}.");
        }
        $this->printTaskInfo("<info>{$this->filename}</info> updated. $count items replaced");
        return Result::success($this, '', ['replaced' => $count]);
    }
}