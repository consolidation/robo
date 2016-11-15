<?php
namespace Robo\Task\ApiGen;

trait loadTasks
{
    /**
     * @param null|string $pathToApiGen
     *
     * @return \Robo\Task\ApiGen\ApiGen
     */
    protected function taskApiGen($pathToApiGen = null)
    {
        return $this->task(ApiGen::class, $pathToApiGen);
    }
}
