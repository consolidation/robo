<?php
namespace Robo\Task;

use Robo\Result;
use Robo\Exception\TaskException;
use Robo\Contract\TaskInterface;
use Symfony\Component\Console\Helper\DialogHelper;
const GITHUB_URL = 'https://api.github.com';

/**
 * Github BundledTasks
 */
trait GitHub
{
    /**
     * @param $tag
     * @return \Robo\Task\Vcs\GitHubRelease
     */
    protected function taskGitHubRelease($tag)
    {
        return new Vcs\GitHubRelease($tag);
    }
}