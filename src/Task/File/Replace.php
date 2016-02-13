<?php
namespace Robo\Task\File;

use Robo\Result;
use Robo\Task\BaseTask;

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
 *
 * $this->taskReplaceInFile('box/robo.txt')
 *  ->from(array('##dbname##', '##dbhost##'))
 *  ->to(array('robo', 'localhost'))
 *  ->run();
 * ?>
 * ```
 *
 * @method regex(string) regex to match string to be replaced
 * @method from(string|array) string(s) to be replaced
 * @method to(string|array) value(s) to be set as a replacement
 */
class Replace extends BaseTask
{
    use \Robo\Common\DynamicParams;

    protected $filename;
    protected $from;
    protected $to;
    protected $regex;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    function run()
    {
        if (!file_exists($this->filename)) {
            $this->printTaskError("File {$this->filename} does not exist");
            return false;
        }

        $text = file_get_contents($this->filename);
        if ($this->regex) {
            $text = preg_replace($this->regex, $this->to, $text, -1, $count);
        } else {
            $text = str_replace($this->from, $this->to, $text, $count);
        }
        if ($count > 0) {
            $res = file_put_contents($this->filename, $text);
            if ($res === false) {
                return Result::error($this, "Error writing to file {$this->filename}.");
            }
            $this->printTaskSuccess("<info>{$this->filename}</info> updated. $count items replaced");
        } else {
            $this->printTaskInfo("<info>{$this->filename}</info> unchanged. $count items replaced");
        }
        return Result::success($this, '', ['replaced' => $count]);
    }
}
