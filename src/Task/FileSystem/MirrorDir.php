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

    /** @var bool $override */
    protected $override = true;
    /** @var bool $copyOnWindows */
    protected $copyOnWindows = true;
    /** @var bool $delete */
    protected $delete = true;

    public function run()
    {
        foreach ($this->dirs as $src => $dst) {
            $this->fs->mirror(
                $src, $dst, null, [
                    'override' => $this->override,
                    'copy_on_windows' => $this->copyOnWindows,
                    'delete' => $this->delete
                ]
            );
            $this->printTaskInfo("Mirrored from <info>$src</info> to <info>$dst</info>");
        }
        return Result::success($this);
    }

    /**
     * Whether to override an existing file on copy or not
     *
     * @param bool $override
     * @return $this
     *
     */
    public function override($override)
    {
        $this->override = $override;
        return $this;
    }

    /**
     * Whether to copy files instead of links on Windows
     *
     * @param bool $copy
     * @return $this
     */
    public function copyOnWindows($copy)
    {
        $this->copyOnWindows = $copy;
        return $this;
    }

    /**
     * Whether to delete files that are not in the source directory
     * 
     * @param bool $delete
     * @return $this
     */
    public function delete($delete)
    {
        $this->delete = $delete;
        return $this;
    }

}
