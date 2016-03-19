<?php
namespace Robo\Task\ApiGen;

use Robo\Container\SimpleServiceProvider;

trait loadTasks
{
    /**
     * Return services.
     */
    public static function getApiGenServices()
    {
        return new SimpleServiceProvider(
            [
                'taskApiGen' => ApiGen::class,
            ]
        );
    }

    /**
     * @param null $pathToApiGen
     * @return \Robo\Task\ApiGen\ApiGen
     */
    protected function taskApiGen($pathToApiGen = null)
    {
        return $this->task(__FUNCTION__, $pathToApiGen);
    }
}
