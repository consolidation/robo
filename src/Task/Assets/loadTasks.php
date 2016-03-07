<?php
namespace Robo\Task\Assets;

trait loadTasks
{
    /**
    * @param $input
    * @return Minify
    */
    protected function taskMinify($input)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Assets\Minify',
            [$input]
        );
    }

    /**
     * @param $input
     * @return ImageMinify
     */
    protected function taskImageMinify($input)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Assets\ImageMinify',
            [$input]
        );
    }

   /**
    * @param $input
    * @return Less
    */
    protected function taskLess($input)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Assets\Less',
            [$input]
        );
    }

    /**
     * @param $input
     * @return Scss
     */
    protected function taskScss($input)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Assets\Scss',
            [$input]
        );
    }
}
