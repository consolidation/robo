<?php
namespace Robo\Task\Testing;

use Robo\Container\SimpleServiceProvider;

trait loadTasks
{
    /**
     * Return services.
     */
    public static function getTestingServices()
    {
        return new SimpleServiceProvider(
            [
                'taskCodecept' => Codecept::class,
                'taskPhpUnit' => PHPUnit::class,
                'taskPhpspec' => Phpspec::class,
                'taskAtoum' => Atoum::class,
            ]
        );
    }

    /**
     * @param null $pathToCodeception
     * @return Codecept
     */
    protected function taskCodecept($pathToCodeception = null)
    {
        return $this->task(__FUNCTION__, $pathToCodeception);
    }

    /**
     * @param null $pathToPhpUnit
     * @return PHPUnit
     */
    protected function taskPhpUnit($pathToPhpUnit = null)
    {
        return $this->task(__FUNCTION__, $pathToPhpUnit);
    }

    /**
     * @param null $pathToPhpspec
     * @return Phpspec
     */
    protected function taskPhpspec($pathToPhpspec = null)
    {
        return $this->task(__FUNCTION__, $pathToPhpspec);
    }

    /**
     * @param null $pathToAtoum
     * @return Atoum
     */
    protected function taskAtoum($pathToAtoum = null)
    {
        return $this->task(__FUNCTION__, $pathToAtoum);
    }
}
