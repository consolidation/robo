<?php
namespace Robo\Task\FileSystem;

use Robo\Result;
use Robo\Exception\TaskException;

/**
 * Copies one dir into another
 *
 * ``` php
 * <?php
 * $this->taskCopyDir(['dist/config' => 'config'])->run();
 * // as shortcut
 * $this->_copyDir('dist/config', 'config');
 * ?>
 * ```
 */
class CopyDir extends BaseDir
{
    public function run()
    {
        foreach ($this->dirs as $src => $dst) {
            $this->copyDir($src, $dst);
            $this->printTaskInfo("Copied from <info>$src</info> to <info>$dst</info>");
        }
        return Result::success($this);
    }

    protected function copyDir($src, $dst)
    {
        $dir = @opendir($src);
        if (false === $dir) {
            throw new TaskException($this, "Cannot open source directory '" . $src . "'");
        }
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file !== '.') && ($file !== '..')) {
                $srcFile = $src . '/' . $file;
                $destFile = $dst . '/' . $file;
                if (is_dir($srcFile)) {
                    $this->copyDir($srcFile, $destFile);
                } else {
                    copy($srcFile, $destFile);
                }
            }
        }
        closedir($dir);
    }
}
