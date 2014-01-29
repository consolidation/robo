<?php
namespace Robo\Task;
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
            FileSystem::doEmptyDir($dir);
            $this->printTaskInfo("cleaned <info>$dir</info>");
        }
    }

}

class CopyDirTask extends BaseDirTask {

    public function run()
    {
        foreach ($this->dirs as $src => $dst) {
            FileSystem::copyDir($src, $dst);
            $this->printTaskInfo("Copied from <info>$src</info> to <info>$dst</info>");
        }
    }
}

class DeleteDirTask extends BaseDirTask {

    public function run()
    {
        foreach ($this->dirs as $dir) {
            FileSystem::deleteDir($dir);
            $this->printTaskInfo("deleted <info>$dir</info>...");
        }
    }
}

class ReplaceInFileTask implements TaskInterface
{
    use \Robo\Output;

    protected $filename;
    protected $from;
    protected $to;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function from($from)
    {
        $this->from = $from;
        return $this;
    }

    public function to($to)
    {
        $this->to = $to;
        return $this;
    }

    function run()
    {
        if (!file_exists($this->filename)) {
            $this->printTaskInfo("<error>File {$this->filename} does not exist</error>");
            return false;
        }
        $text = file_get_contents($this->filename);
        $text = str_replace($this->from, $this->to, $text);
        $res = file_put_contents($this->filename, $text);
        if ($res === false) {
            $this->printTaskInfo("<error>Error writing to file {$this->filename}</error>");
        } else {
            $this->printTaskInfo("{$this->filename} updated");
        }
        return $res;
    }

}
