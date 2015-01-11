<?php
namespace Robo\Task;

use Robo\Output;
use Robo\Result;
use Robo\Contract\CommandInterface;
use Robo\Task\CommandStack;
use Robo\Contract\TaskInterface;

trait Git {

    protected function taskGitStack($pathToGit = 'git')
    {
        return new Vcs\GitStack($pathToGit);
    }

}