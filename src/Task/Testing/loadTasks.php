<?php
namespace Robo\Task\Testing;

trait loadTasks
{
    /**
     * @param null|string $pathToCodeception
     *
     * @return \Robo\Task\Testing\Codecept
     */
    protected function taskCodecept($pathToCodeception = null)
    {
        return $this->task(Codecept::class, $pathToCodeception);
    }

    /**
     * @param null|string $pathToPhpUnit
     *
     * @return \Robo\Task\Testing\PHPUnit
     */
    protected function taskPhpUnit($pathToPhpUnit = null)
    {
        return $this->task(PHPUnit::class, $pathToPhpUnit);
    }

    /**
     * @param null $pathToPhpspec
     *
     * @return \Robo\Task\Testing\Phpspec
     */
    protected function taskPhpspec($pathToPhpspec = null)
    {
        return $this->task(Phpspec::class, $pathToPhpspec);
    }

    /**
     * @param null $pathToAtoum
     *
     * @return \Robo\Task\Testing\Atoum
     */
    protected function taskAtoum($pathToAtoum = null)
    {
        return $this->task(Atoum::class, $pathToAtoum);
    }

    /**
     * @param null $pathToBehat
     *
     * @return \Robo\Task\Testing\Behat
     */
    protected function taskBehat($pathToBehat = null)
    {
        return $this->task(Behat::class, $pathToBehat);
    }
}
