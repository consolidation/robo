<?php
namespace Robo\Task;

use Robo\Output;
use Robo\Result;
use Robo\Task\Shared\CommandInterface;
use Robo\Task\Shared\CommandStack;
use Robo\Task\Shared\TaskInterface;

trait Git {

    protected function taskGitStack($pathToGit = 'git')
    {
        return new Vcs\GitStack($pathToGit);
    }

}