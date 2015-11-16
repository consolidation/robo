<?php
namespace Robo\Task\FileSystem;

use Robo\Common\FileAndFolderCheck;
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
    use FileAndFolderCheck;

    public function run()
    {
        if (!$this->checkResources($this->dirs, 'dir')) {
            return Result::error($this, 'Resources are missing!');
        }
        foreach ($this->dirs as $dir) {
            $this->fs->remove($dir);
            $this->printTaskInfo("Deleted <info>$dir</info>...");
        }
        return Result::success($this);
    }
}
