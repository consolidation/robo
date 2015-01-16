<?php
namespace Robo\Task\FileSystem;

use Robo\Result;

/**
 * Deletes all files from specified dir, ignoring git files.
 *
 * ``` php
 * <?php
 * $this->taskCleanDir(['tmp','logs'])->run();
 * // as shortcut
 * $this->_cleanDir('app/cache');
 * ?>
 * ```
 */
class CleanDir extends BaseDir
{
    public function run()
    {
        foreach ($this->dirs as $dir) {
            $this->emptyDir($dir);
            $this->printTaskInfo("cleaned <info>$dir</info>");
        }
        return Result::success($this);
    }

    protected function emptyDir($path)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $path) {
            if ($path->isDir()) {
                $dir = (string)$path;
                if (basename($dir) === '.' || basename($dir) === '..') {
                    continue;
                }
                $this->fs->remove($dir);
            } else {
                $file = (string)$path;
                if (basename($file) === '.gitignore' || basename($file) === '.gitkeep') {
                    continue;
                }
                $this->fs->remove($file);
            }
        }
    }
}