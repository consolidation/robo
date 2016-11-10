<?php
namespace Robo\Task\Assets;

trait loadTasks
{
    /**
    * @param string $input
    *
     * @return \Robo\Task\Assets\Minify
    */
    protected function taskMinify($input)
    {
        return $this->task(Minify::class, $input);
    }

    /**
     * @param string|string[] $input
     *
     * @return \Robo\Task\Assets\ImageMinify
     */
    protected function taskImageMinify($input)
    {
        return $this->task(ImageMinify::class, $input);
    }

   /**
    * @param array $input
    *
    * @return \Robo\Task\Assets\Less
    */
    protected function taskLess($input)
    {
        return $this->task(Less::class, $input);
    }

    /**
     * @param array $input
     *
     * @return \Robo\Task\Assets\Scss
     */
    protected function taskScss($input)
    {
        return $this->task(Scss::class, $input);
    }
}
