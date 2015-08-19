<?php
namespace Robo\Task\Assets;

trait loadTasks {
    /**
    * @param $input
    * @return Minify
    */
    protected function taskMinify($input)
    {
        return new Minify($input);
    }

   /**
    * @param $input
    * @return Minify
    */
    protected function taskLess($input)
    {
        return new Less($input);
    }

    /**
     * @param $input
     * @return Scss
     */
    protected function taskScss($input)
    {
        return new Scss($input);
    }
}
