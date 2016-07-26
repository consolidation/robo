<?php
namespace Codeception\Module;

use Robo\Robo;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;

class CliHelper extends \Codeception\Module implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    use \Robo\LoadAllTasks {
        collection as public;
        collectionBuilder as public;
        task as public;
        taskExec as public;
        taskExecStack as public;
        taskWriteToFile as public;
        taskReplaceInFile as public;
        taskConcat as public;
        taskTmpFile as public;
        taskCleanDir as public;
        taskCopyDir as public;
        taskGenTask as public;
        taskDeleteDir as public;
        taskFlattenDir as public;
        taskFilesystemStack as public;
        taskTmpDir as public;
        _copyDir as public shortcutCopyDir;
        _mirrorDir as public shortcutMirrorDir;
        _tmpDir as public shortcutTmpDir;
        taskPack as public;
        taskExtract as public;
    }

    public function seeDirFound($dir)
    {
        $this->assertTrue(is_dir($dir) && file_exists($dir), "Directory does not exist");
    }

    public function _before(\Codeception\TestCase $test) {
        $this->getModule('Filesystem')->copyDir(codecept_data_dir().'claypit', codecept_data_dir().'sandbox');
        $this->setContainer(Robo::getContainer());
        $this->getContainer()->add('output', new NullOutput());
    }

    public function _after(\Codeception\TestCase $test) {
        $this->getModule('Filesystem')->deleteDir(codecept_data_dir().'sandbox');
        $this->getContainer()->add('output', new ConsoleOutput());
        chdir(codecept_root_dir());
    }
}
