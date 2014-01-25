<?php
namespace Robo\Task;
use Robo\Util\FileSystem;

class CopyDir extends BaseDir {

    public function run()
    {
        foreach ($this->dirs as $src => $dst) {
            FileSystem::copyDir($src, $dst);
            $this->printTaskInfo("Copied from <info>$src</info> to <info>$dst</info>");
        }
    }

}
 