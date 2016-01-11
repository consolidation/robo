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
        taskTmpFile as public;
    }

   	use \Robo\Task\FileSystem\loadTasks {
        taskCleanDir as public;
        taskCopyDir as public;
        taskDeleteDir as public;
        taskFlattenDir as public;
        taskFileSystemStack as public;
        taskTmpDir as public;
    }

    use \Robo\Task\FileSystem\loadShortcuts {
        _copyDir as public shortcutCopyDir;
        _mirrorDir as public shortcutMirrorDir;
        _tmpDir as public shortcutTmpDir;
    }

    use \Robo\Collection\loadTasks {
        collection as public;
    }

    use \Robo\Task\Archive\loadTasks {
        taskPack as public;
        taskExtract as public;
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
