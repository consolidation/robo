<?php
namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Symfony\Component\Console\Output\NullOutput;

class CliHelper extends \Codeception\Module
{
//   	use \Robo\Task\Development;
   	use \Robo\Task\Exec {
        taskExec as public;
        taskExecStack as public;
    }
   	use \Robo\Task\FileSystem {
        taskCleanDir as public;
        taskCopyDir as public;
        taskDeleteDir as public;
        taskWriteToFile as public;
        taskReplaceInFile as public;
        taskRequire as public;
        taskFileSystemStack as public;
    }
    use \Robo\Task\Concat {
        taskConcat as public;
    }

    public function seeDirFound($dir)
    {
        $this->assertTrue(is_dir($dir) && file_exists($dir), "Directory does not exist");
    }

    public function _before(\Codeception\TestCase $test) {
        $this->getModule('Filesystem')->copyDir(codecept_data_dir().'claypit', codecept_data_dir().'sandbox');
        \Robo\Runner::setPrinter(new NullOutput());
    }

    public function _after(\Codeception\TestCase $test) {
        $this->getModule('Filesystem')->deleteDir(codecept_data_dir().'sandbox');
        \Robo\Runner::setPrinter(null);
        chdir(codecept_root_dir());
    }
}