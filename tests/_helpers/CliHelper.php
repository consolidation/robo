<?php
namespace Codeception\Module;

use Robo\Config;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;

class CliHelper extends \Codeception\Module
{
   	use \Robo\Task\Base\loadTasks {
        taskExec as public;
        taskExecStack as public;
    }
    use \Robo\Task\File\loadTasks {
        taskWriteToFile as public;
        taskReplaceInFile as public;
        taskConcat as public;
    }

   	use \Robo\Task\FileSystem\loadTasks {
        taskCleanDir as public;
        taskCopyDir as public;
        taskDeleteDir as public;
        taskFileSystemStack as public;
    }

    use \Robo\Task\FileSystem\loadShortcuts {
        _copyDir as public shortcutCopyDir;
        _mirrorDir as public shortcutMirrorDir;
    }

    public function seeDirFound($dir)
    {
        $this->assertTrue(is_dir($dir) && file_exists($dir), "Directory does not exist");
    }

    public function _before(\Codeception\TestCase $test) {
        $this->getModule('Filesystem')->copyDir(codecept_data_dir().'claypit', codecept_data_dir().'sandbox');
        Config::setOutput(new NullOutput());
    }

    public function _after(\Codeception\TestCase $test) {
        $this->getModule('Filesystem')->deleteDir(codecept_data_dir().'sandbox');
        Config::setOutput(new ConsoleOutput());
        chdir(codecept_root_dir());
    }
}
