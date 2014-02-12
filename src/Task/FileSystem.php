<?php
namespace Robo\Task;
use Robo\Output;
use Robo\Result;
use Robo\Util\FileSystem as FSUtils;

trait FileSystem
{
    /**
     * @param $dirs
     * @return CleanDir
     */
    protected function taskCleanDir($dirs)
    {
        return new CleanDirTask($dirs);
    }

    /**
     * @param $dirs
     * @return DeleteDir
     */
    protected function taskDeleteDir($dirs)
    {
        return new DeleteDirTask($dirs);
    }

    /**
     * @param $dirs
     * @return CopyDir
     */
    protected function taskCopyDir($dirs)
    {
        return new CopyDirTask($dirs);
    }

    protected function taskReplaceInFile($file)
    {
        return new ReplaceInFileTask($file);
    }

    protected function taskWriteToFile($file)
    {
        return new WriteToFileTask($file);
    }

    protected function taskRequire($file)
    {
        return new RequireTask($file);
    }
}

abstract class BaseDirTask implements TaskInterface {
    use \Robo\Output;

    protected $dirs = [];

    public function __construct($dirs)
    {
        is_array($dirs)
            ? $this->dirs = $dirs
            : $this->dirs[] = $dirs;
    }

}

class CleanDirTask extends BaseDirTask {

    public function run()
    {
        foreach ($this->dirs as $dir) {
            FSUtils::doEmptyDir($dir);
            $this->printTaskInfo("cleaned <info>$dir</info>");
        }
        return Result::success($this);
    }

}

class CopyDirTask extends BaseDirTask {

    public function run()
    {
        foreach ($this->dirs as $src => $dst) {
            FSUtils::copyDir($src, $dst);
            $this->printTaskInfo("Copied from <info>$src</info> to <info>$dst</info>");
        }
        return Result::success($this);
    }
}

class DeleteDirTask extends BaseDirTask {

    public function run()
    {
        foreach ($this->dirs as $dir) {
            FSUtils::deleteDir($dir);
            $this->printTaskInfo("deleted <info>$dir</info>...");
        }
        return Result::success($this);
    }
}

/**
 * @method ReplaceInFileTask filename(string)
 * @method ReplaceInFileTask from(string)
 * @method ReplaceInFileTask to(string)
 *
 * Class ReplaceInFileTask
 * @package Robo\Task
 */
class ReplaceInFileTask implements TaskInterface
{
    use \Robo\Output;
    use DynamicConfig;

    protected $filename;
    protected $from;
    protected $to;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    function run()
    {
        if (!file_exists($this->filename)) {
            $this->printTaskInfo("<error>File {$this->filename} does not exist</error>");
            return false;
        }
        $text = file_get_contents($this->filename);
        $text = str_replace($this->from, $this->to, $text, $count);
        $res = file_put_contents($this->filename, $text);
        if ($res === false) {
            return Result::error($this, "Error writing to file {$this->filename}.");
        }
        $this->printTaskInfo("{$this->filename} updated. $count items replaced");
        return Result::success($this, '', ['replaced' => $count]);
    }
}

class WriteToFileTask implements TaskInterface
{
    use Output;
    protected $filename;
    protected $body = "";

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function line($line)
    {
        $this->body .= $line."\n";
        return $this;
    }

    public function lines($lines)
    {
        $this->body .= implode("\n", $lines)."\n";
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
        $this->body = str_replace("{{$name}}",$val, $this->body);
        return $this;
    }

    public function run()
    {
        $this->printTaskInfo("Writing to {$this->filename}.");
        $res = file_put_contents($this->filename, $this->body);
        if ($res === false) return Result::error($this, "File {$this->filename} couldnt be created");
        return Result::success($this);
    }
}

class RequireTask
{
    protected $file;
    protected $locals = [];

    function __construct($pathToRequiredFile)
    {
        $this->file = $pathToRequiredFile;
    }

    public function local(array $locals)
    {
        $this->locals = array_merge($this->locals, $locals);
    }

    public function run()
    {
        extract($this->locals);
        if (!file_exists($this->file)) {
            return Result::error($this, "File {$this->file} does not exists and cant be required.");
        }
        @require $this->file;

        return Result::success($this);
    }
}
