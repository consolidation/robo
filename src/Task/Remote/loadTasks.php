<?php
namespace Robo\Task\Remote;

use Robo\Container\SimpleServiceProvider;

trait loadTasks
{
    /**
     * Return services.
     */
    public static function getRemoteServices()
    {
        return new SimpleServiceProvider(
            [
                'taskRsync' => Rsync::class,
                'taskSshExec' => Ssh::class,
            ]
        );
    }

    /**
     * @return Rsync
     */
    protected function taskRsync()
    {
        return $this->task(__FUNCTION__);
    }

    /**
     * @param null $hostname
     * @param null $user
     * @return Ssh
     */
    protected function taskSshExec($hostname = null, $user = null)
    {
        return $this->task(__FUNCTION__, $hostname, $user);
    }
}
