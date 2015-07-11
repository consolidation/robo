<?php
namespace Robo\Task\FileSystem;

use Robo\Result;

/**
 * Deletes dir
 *
 * ``` php
 * <?php
 * $this->taskDeleteDir('tmp')->run();
 * // as shortcut
 * $this->_deleteDir(['tmp', 'log']);
 * ?>
 * ```
 */
class DeleteDir extends BaseDir
{
    public function run()
    {
        foreach ($this->dirs as $dir) {
            $this->fs->remove($dir);
            $this->printTaskInfo("Deleted <info>$dir</info>...");
        }
        return Result::success($this);
    }
}
