<?php
namespace Robo\Task\Bower;

trait loadTasks
{
    /**
     * @param null $pathToBower
     * @return Install
     */
    protected function taskBowerInstall($pathToBower = null)
    {
        return $this->task(Install::class, $pathToBower);
    }

    /**
     * @param null $pathToBower
     * @return Update
     */
    protected function taskBowerUpdate($pathToBower = null)
    {
        return $this->task(Update::class, $pathToBower);
    }
}
