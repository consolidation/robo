<?php
namespace Robo\Task\Remote;

trait loadTasks 
{
    /**
     * @return Rsync
     */
    protected function taskRsync()
    {
        return new Rsync();
    }

    /**
     * @param null $hostname
     * @param null $user
     * @return Ssh
     */
    protected function taskSshExec($hostname = null, $user = null)
    {
        return new Ssh($hostname, $user);
    }

} 