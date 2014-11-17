<?php
namespace Robo\Task;
use Robo\Output;
use Robo\Result;
use Robo\Task\Shared\DynamicConfig;
use Robo\Task\Shared\TaskInterface;
use Robo\Util\FileSystem as UtilsFileSystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Contains useful tasks to work with filesystem.
 *
 */
trait FileSystem
{
    /**
     * @param $dirs
     * @return CleanDirTask
     */
    protected function taskCleanDir($dirs)
    {
        return new CleanDirTask($dirs);
    }

    /**
     * @param $dirs
     * @return DeleteDirTask
     */
    protected function taskDeleteDir($dirs)
    {
        return new DeleteDirTask($dirs);
    }

    /**
     * @param $dirs
     * @return CopyDirTask
     */
    protected function taskCopyDir($dirs)
    {
        return new CopyDirTask($dirs);
    }

    /**
     * @param $dirs
     * @return MirrorDirTask
     */
    protected function taskMirrorDir($dirs)
    {
        return new MirrorDirTask($dirs);
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

    protected function taskFileSystemStack()
    {
        return new FileSystemStackTask();
    }
}

abstract class BaseDirTask implements TaskInterface {
    use Output;

    protected $dirs = [];

    protected $fs;

    public function __construct($dirs)
    {
        is_array($dirs)
            ? $this->dirs = $dirs
            : $this->dirs[] = $dirs;

        $this->fs = new UtilsFileSystem;
    }

}

/**
 * Deletes all files from specified dir, ignoring git files.
 *
 * ``` php
 * <?php
 * $this->taskCleanDir('app/cache')->run();
 * $this->taskCleanDir(['tmp','logs'])->run();
 * ?>
 * ```
 */
class CleanDirTask extends BaseDirTask {

    public function run()
    {
        foreach ($this->dirs as $dir) {
            $this->fs->doEmptyDir($dir);
            $this->printTaskInfo("cleaned <info>$dir</info>");
        }
        return Result::success($this);
    }

}

/**
 * Copies one dir into another
 *
 * ``` php
 * <?php
 * $this->taskCopyDir(['dist/config' => 'config'])->run();
 * ?>
 * ```
 */
class CopyDirTask extends BaseDirTask {

    public function run()
    {
        foreach ($this->dirs as $src => $dst) {
            $this->fs->copyDir($src, $dst);
            $this->printTaskInfo("Copied from <info>$src</info> to <info>$dst</info>");
        }
        return Result::success($this);
    }
}

/**
 * Deletes dir
 *
 * ``` php
 * <?php
 * $this->taskDeleteDir('tmp')->run();
 * $this->taskDeleteDir(['tmp', 'log'])->run();
 * ?>
 * ```
 */
class DeleteDirTask extends BaseDirTask {

    public function run()
    {
        foreach ($this->dirs as $dir) {
            $this->fs->deleteDir($dir);
            $this->printTaskInfo("deleted <info>$dir</info>...");
        }
        return Result::success($this);
    }
}

/**
 * Mirrors a directory to another
 *
 * ``` php
 * <?php
 * $this->taskMirrorDir(['dist/config/' => 'config/'])->run();
 * ?>
 * ```
 */
class MirrorDirTask extends BaseDirTask {

    public function run()
    {
        foreach ($this->dirs as $src => $dst) {
            $this->fs->mirror($src, $dst, null, [
                'override'        => true,
                'copy_on_windows' => true,
                'delete'          => true
            ]);
            $this->printTaskInfo("Mirrored from <info>$src</info> to <info>$dst</info>");
        }
        return Result::success($this);
    }
}

/**
 * Performs search and replace inside a files.
 *
 * ``` php
 * <?php
 * $this->replaceInFile('VERSION')
 *  ->from('0.2.0')
 *  ->to('0.3.0')
 *  ->run();
 *
 * $this->replaceInFile('README.md')
 *  ->from(date('Y')-1)
 *  ->to(date('Y'))
 *  ->run();
 *
 * $this->replaceInFile('config.yml')
 *  ->regex('~^service:~')
 *  ->to('services:')
 *  ->run();
 * ?>
 * ```
 *
 * @method \Robo\Task\ReplaceInFileTask regex(string)
 * @method \Robo\Task\ReplaceInFileTask from(string)
 * @method \Robo\Task\ReplaceInFileTask to(string)
 */
class ReplaceInFileTask implements TaskInterface
{
    use Output;
    use DynamicConfig;

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
 * @method \Robo\Task\WriteToFileTask append()
 */
class WriteToFileTask implements TaskInterface
{
    use Output;
    use DynamicConfig;

    protected $filename;
    protected $body = "";
    protected $append = false;

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
        $this->printTaskInfo("Writing to <info>{$this->filename}</info>.");
        if ($this->append) {
            $this->body = file_get_contents($this->filename).$this->body;
        }
        if (!file_exists(dirname($this->filename))) {
            mkdir(dirname($this->filename),0777,true);
        }
        $res = file_put_contents($this->filename, $this->body);
        if ($res === false) return Result::error($this, "File {$this->filename} couldnt be created");
        return Result::success($this);
    }
}

/**
 * Requires php file to be executed inside a closure.
 *
 * ``` php
 * <?php
 * $this->taskRequire('script/create_users.php')->run();
 * $this->taskRequire('script/make_admin.php')
 *  ->locals(['user' => $user])
 *  ->run();
 * ?>
 * ```
 */
class RequireTask implements TaskInterface
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

/**
 * Wrapper for [Symfony FileSystem](http://symfony.com/doc/current/components/filesystem.html) Component.
 * Comands are executed in stack and can be stopped on first fail with `stopOnFail` option.
 *
 * ``` php
 * <?php
 * $this->taskFileSystemStack()
 *      ->mkdir('logs')
 *      ->touch('logs/.gitignore')
 *      ->chgrp('www', 'www-data')
 *      ->symlink('/var/log/nginx/error.log', 'logs/error.log')
 *      ->run();
 * ?>
 * ```
 *
 * Class FileSystemStackTask
 * @package Robo\Task
 */
class FileSystemStackTask implements TaskInterface
{
    use \Robo\Output;
    protected $stack = [];

    protected $stopOnFail = false;

    public function stopOnFail($stop = true)
    {
        $this->stopOnFail = $stop;
        return $this;
    }

    public function mkdir($dir)
    {
        $this->stack[] = array_merge([__FUNCTION__], func_get_args());
        return $this;
    }

    public function touch($file)
    {
        $this->stack[] = array_merge([__FUNCTION__], func_get_args());
        return $this;
    }

    public function copy($from, $to, $force = false)
    {
        $this->stack[] = array_merge([__FUNCTION__], compact('from', 'to', 'force'));
        return $this;
    }

    public function chmod($file, $permissions, $umask = 0000, $recursive = true)
    {
        $this->stack[] = array_merge([__FUNCTION__], compact('file', 'permissions', 'umask', 'recursive'));
        return $this;
    }

    public function remove($file)
    {
        $this->stack[] = array_merge([__FUNCTION__], func_get_args());
        return $this;
    }

    public function rename($from, $to)
    {
        $this->stack[] = array_merge([__FUNCTION__], func_get_args());
        return $this;
    }

    public function symlink($from, $to)
    {
        $this->stack[] = array_merge([__FUNCTION__], func_get_args());
        return $this;
    }

    public function mirror($from, $to)
    {
        $this->stack[] = array_merge([__FUNCTION__], func_get_args());
        return $this;
    }

    public function chgrp($file, $group)
    {
        $this->stack[] = array_merge([__FUNCTION__], func_get_args());
        return $this;
    }

    public function chown($file, $user)
    {
        $this->stack[] = array_merge([__FUNCTION__], func_get_args());
        return $this;
    }

    public function run()
    {
        $fs = new UtilsFileSystem();
        $code = 0;
        foreach ($this->stack as $action) {
            $command = array_shift($action);
            if (!method_exists($fs, $command)) continue;
            $this->printTaskInfo("$command ".json_encode($action));
            try {
                call_user_func_array([$fs, $command], $action);
            } catch (IOExceptionInterface $e) {
                if ($this->stopOnFail) {
                    return Result::error($this, $e->getMessage(), $e->getPath());
                }
                $code = 1;
                $this->printTaskInfo("<error>".$e->getMessage()."</error>");
            }
        }
        return new Result($this, $code);
    }

}