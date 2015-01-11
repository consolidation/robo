<?php
namespace Robo\Task\Testing;

trait loadTasks 
{
    /**
     * @param null $pathToCodeception
     * @return CodeceptRun
     */
    protected function taskCodecept($pathToCodeception = null)
    {
        return new CodeceptRun($pathToCodeception);
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