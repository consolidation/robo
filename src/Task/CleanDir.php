<?php
namespace Robo\Task;
use Robo\Util\FileSystem;

class CleanDir extends BaseDir {

    public function run()
    {
        foreach ($this->dirs as $dir) {
            FileSystem::doEmptyDir($dir);
            $this->tas("cleaned $dir");
        }
    }

}
 