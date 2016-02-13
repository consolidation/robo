<?php
namespace Robo\Collection;

trait loadTasks
{
    /**
     * @param $dirs
     * @return CleanDir
     */
    protected function collection()
    {
        return new Collection();
    }
}
