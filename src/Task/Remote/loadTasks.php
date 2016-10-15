<?php
namespace Robo\Task\Remote;

trait loadTasks
{
    /**
     * @return \Robo\Task\Remote\Rsync
     */
    protected function taskRsync()
    {
        return $this->task(Rsync::class);
    }

    /**
     * @param null|string $hostname
     * @param null|string $user
     *
     * @return \Robo\Task\Remote\Ssh
     */
    protected function taskSshExec($hostname = null, $user = null)
    {
        return $this->task(Ssh::class, $hostname, $user);
    }
}
