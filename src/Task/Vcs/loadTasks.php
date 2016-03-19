<?php
namespace Robo\Task\Vcs;

use Robo\Container\SimpleServiceProvider;

trait loadTasks
{
    /**
     * Return services.
     */
    public static function getVcsServices()
    {
        return new SimpleServiceProvider(
            [
                'taskSvnStack' => SvnStack::class,
                'taskGitStack' => GitStack::class,
            ]
        );
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $pathToSvn
     * @return SvnStack
     */
    protected function taskSvnStack($username = '', $password = '', $pathToSvn = 'svn')
    {
        return $this->task(__FUNCTION__, $username, $password, $pathToSvn);
    }

    /**
     * @param string $pathToGit
     * @return GitStack
     */
    protected function taskGitStack($pathToGit = 'git')
    {
        return $this->task(__FUNCTION__, $pathToGit);
    }
}
