<?php
namespace Robo\Task\FileSystem;

use Robo\Result;

/**
 * Mirrors a directory to another
 *
 * ``` php
 * <?php
 * $this->taskMirrorDir(['dist/config/' => 'config/'])->run();
 * // or use shortcut
 * $this->_mirrorDir('dist/config/', 'config/');
 *
 * ?>
 * ```
 */
class MirrorDir extends BaseDir
{
    public function run()
    {
        foreach ($this->dirs as $src => $dst) {
            $this->fs->mirror(
                $src, $dst, null, [
                    'override' => true,
                    'copy_on_windows' => true,
                    'delete' => true
                ]
            );
            $this->printTaskInfo("Mirrored from <info>$src</info> to <info>$dst</info>");
        }
        return Result::success($this);
    }
}
