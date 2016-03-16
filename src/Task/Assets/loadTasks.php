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
        return $this->task('Minify', $input);
    }

    /**
     * @param $input
     * @return ImageMinify
     */
    protected function taskImageMinify($input)
    {
        return $this->task('ImageMinify', $input);
    }

   /**
    * @param $input
    * @return Less
    */
    protected function taskLess($input)
    {
        return $this->task('Less', $input);
    }

    /**
     * @param $input
     * @return Scss
     */
    protected function taskScss($input)
    {
        return $this->task('Scss', $input);
    }
}
