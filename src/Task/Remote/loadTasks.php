<?php
namespace Robo\Task\Remote;

trait loadTasks
{
    /**
     * @return Rsync
     */
    protected function taskRsync()
    {
        return $this->task('Rsync');
    }

    /**
     * @param null $hostname
     * @param null $user
     * @return Ssh
     */
    protected function taskSshExec($hostname = null, $user = null)
    {
        return $this->task('Ssh', $hostname, $user);
    }
}
