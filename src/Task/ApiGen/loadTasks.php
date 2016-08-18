<?php
namespace Robo\Task\ApiGen;

use Robo\Container\SimpleServiceProvider;

trait loadTasks
{
    /**
     * @param null $pathToApiGen
     * @return \Robo\Task\ApiGen\ApiGen
     */
    protected function taskApiGen($pathToApiGen = null)
    {
        return $this->task(ApiGen::class, $pathToApiGen);
    }
}
