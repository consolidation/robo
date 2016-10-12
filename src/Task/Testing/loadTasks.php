<?php
namespace Robo\Task\Testing;

trait loadTasks
{
    /**
     * @param null $pathToCodeception
     * @return Codecept
     */
    protected function taskCodecept($pathToCodeception = null)
    {
        return $this->task(Codecept::class, $pathToCodeception);
    }

    /**
     * @param null $pathToPhpUnit
     * @return PHPUnit
     */
    protected function taskPhpUnit($pathToPhpUnit = null)
    {
        return $this->task(PHPUnit::class, $pathToPhpUnit);
    }

    /**
     * @param null $pathToPhpspec
     * @return Phpspec
     */
    protected function taskPhpspec($pathToPhpspec = null)
    {
        return $this->task(Phpspec::class, $pathToPhpspec);
    }

    /**
     * @param null $pathToAtoum
     * @return Atoum
     */
    protected function taskAtoum($pathToAtoum = null)
    {
        return $this->task(Atoum::class, $pathToAtoum);
    }

    /**
     * @param null $pathToBehat
     * @return Behat
     */
    protected function taskBehat($pathToBehat = null)
    {
        return $this->task(Behat::class, $pathToBehat);
    }
}
