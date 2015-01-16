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
        return new Codecept($pathToCodeception);
    }

    /**
     * @param null $pathToPhpUnit
     * @return PHPUnit
     */
    protected function taskPhpUnit($pathToPhpUnit = null)
    {
        return new PHPUnit($pathToPhpUnit);
    }

    /**
     * @param null $pathToPhpspec
     * @return Phpspec
     */
    protected function taskPhpspec($pathToPhpspec = null)
    {
        return new Phpspec($pathToPhpspec);
    }
} 