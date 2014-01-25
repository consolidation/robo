<?php
namespace Robo\Task;
use Robo\Task;
use Robo\Util\FileSystem;

class DeleteDir extends BaseDir {

    public function run()
    {
        foreach ($this->dirs as $dir) {
            FileSystem::deleteDir($dir);
            $this->printTaskInfo("deleted <info>$dir</info>...");
        }
    }

}
