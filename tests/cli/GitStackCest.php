<?php
namespace Robo;

use \CliGuy;

use Robo\Contract\TaskInterface;
use Robo\Collection\Temporary;
use Robo\Result;

class GitStackCest
{
    public function _before(CliGuy $I)
    {
        $I->getContainer()->addServiceProvider(\Robo\Task\Vcs\loadTasks::getVcsServices());
        $I->getContainer()->addServiceProvider(\Robo\Task\File\loadTasks::getFileServices());
        $I->getContainer()->addServiceProvider(\Robo\Task\FileSystem\loadTasks::getFileSystemServices());

        $I->amInPath(codecept_data_dir().'sandbox');
    }

    public function toCreateGitRepository(CliGuy $I)
    {
        // Set up a filesystem stack, but use addToCollection() to defer execution
        $result = $I->taskFileSystemStack()
            ->mkdir('project')
            ->touch('project/README.md')
            ->run();

        $I->assertEquals(0, $result->getExitCode(), $result->getMessage());
        $I->seeFileFound('project/README.md');

        $result = $I->taskGitStack()
            ->init()
            ->add('-A')
            ->commit('Initial commit')
            ->run();

        $I->assertEquals(0, $result->getExitCode(), $result->getMessage());

        $result = $I->taskGitStack()
            ->checkout('-b', 'first')
            ->run();

        $I->assertEquals(0, $result->getExitCode(), $result->getMessage());

        $result = $I->taskFileSystemStack()
            ->mkdir('project')
            ->touch('project/first.txt')
            ->run();

        $I->assertEquals(0, $result->getExitCode(), $result->getMessage());
        $I->seeFileFound('project/first.txt');

        $result = $I->taskGitStack()
            ->add('-A')
            ->commit('Commit on first branch')
            ->run();
        $I->assertEquals(0, $result->getExitCode(), $result->getMessage());

        $result = $I->taskGitStack()
            ->checkout('master')
            ->run();
        $I->assertEquals(0, $result->getExitCode(), $result->getMessage());

        $I->dontSeeFileFound('project/first.txt');
    }
}
