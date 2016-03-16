<?php
namespace Robo\Task\Vcs;

trait loadTasks
{
    /**
     * @param string $username
     * @param string $password
     * @param string $pathToSvn
     * @return SvnStack
     */
    protected function taskSvnStack($username = '', $password = '', $pathToSvn = 'svn')
    {
        return $this->task('SvnStack', $username, $password, $pathToSvn);
    }

    /**
     * @param string $pathToGit
     * @return GitStack
     */
    protected function taskGitStack($pathToGit = 'git')
    {
        return $this->task('GitStack', $pathToGit);
    }
}
