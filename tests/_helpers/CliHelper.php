<?php
namespace Codeception\Module;

use Robo\Robo;
use Robo\Collection\CollectionBuilder;
use Robo\Task\ValueProviderTask;

use Symfony\Component\Console\Output\ConsoleOutput;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;

class CliHelper extends \Codeception\Module implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use SeeInOutputTrait;

    use \Robo\LoadAllTasks {
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
        taskMinify as public;
        _copyDir as public shortcutCopyDir;
        _mirrorDir as public shortcutMirrorDir;
        _tmpDir as public shortcutTmpDir;
        taskPack as public;
        taskExtract as public;
        setBuilder as public;
    }

    public function collectionBuilder()
    {
        $tasks = new CliHelperTasks();
        $builder = CollectionBuilder::create($this->getContainer(), $tasks);
        $tasks->setBuilder($builder);

        return $builder;
    }

    public function seeDirFound($dir)
    {
        $this->assertTrue(is_dir($dir) && file_exists($dir), "Directory does not exist");
    }

    public function _before(\Codeception\TestCase $test) {
        $container = new \League\Container\Container();
        $this->initSeeInOutputTrait($container);
        Robo::setContainer($container);
        $this->setContainer($container);

        $this->getModule('Filesystem')->copyDir(codecept_data_dir().'claypit', codecept_data_dir().'sandbox');
    }

    public function _after(\Codeception\TestCase $test) {
        $this->getModule('Filesystem')->deleteDir(codecept_data_dir().'sandbox');
        $this->getContainer()->add('output', new ConsoleOutput());
        chdir(codecept_root_dir());
    }
}
