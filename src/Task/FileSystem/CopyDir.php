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
    /** @var int $chmod */
    protected $chmod = 0755;

    public function run()
    {
        foreach ($this->dirs as $src => $dst) {
            $this->copyDir($src, $dst);
            $this->printTaskInfo("Copied from <info>$src</info> to <info>$dst</info>");
        }
        return Result::success($this);
    }

    /**
     * Sets the default folder permissions for the destination if it doesn't exist
     *
     * @link http://en.wikipedia.org/wiki/Chmod
     * @link http://php.net/manual/en/function.mkdir.php
     * @link http://php.net/manual/en/function.chmod.php
     * @param int $value
     * @return $this
     */
    public function dirPermissions($value)
    {
        $this->chmod = (int)$value;
        return $this;
    }

    /**
     * Copies a directory to another location.
     *
     * @param string $src Source directory
     * @param string $dst Destination directory
     * @throws \Robo\Exception\TaskException
     * @return void
     */
    protected function copyDir($src, $dst)
    {
        $dir = @opendir($src);
        if (false === $dir) {
            throw new TaskException($this, "Cannot open source directory '" . $src . "'");
        }
        if (!is_dir($dst)) {
            mkdir($dst, $this->chmod, true);
        }
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
