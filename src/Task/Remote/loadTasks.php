<?php
namespace Robo\Task\Remote;

use Robo\Container\SimpleServiceProvider;

trait loadTasks
{
    /**
     * @return Rsync
     */
    protected function taskRsync()
    {
        return $this->task(Rsync::class);
    }

    /**
     * @param null $hostname
     * @param null $user
     * @return Ssh
     */
    protected function taskSshExec($hostname = null, $user = null)
    {
        return $this->task(Ssh::class, $hostname, $user);
    }
}
