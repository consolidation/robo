<?php
namespace Robo\Task;
use Robo\Util\FileSystem;

class CopyDir extends BaseDir {

    public function run()
    {
        foreach ($this->dirs as $src => $dst) {
            FileSystem::copyDir($src, $dst);
            $this->say("Copied from $src to $dst");
        }
    }

}
 