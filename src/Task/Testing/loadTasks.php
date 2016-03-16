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
}
