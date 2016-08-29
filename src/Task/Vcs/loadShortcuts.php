<?php
namespace Robo\Task\Vcs;

trait loadShortcuts
{
    /**
     * @param $url
     * @return mixed
     */
    protected function _svnCheckout($url)
    {
        return $this->taskSvnStack()->checkout($url)->run();
    }

    /**
     * @param $url
     * @return \Robo\Result
     */
    protected function _gitClone($url)
    {
        return $this->taskGitStack()->cloneRepo($url)->run();
    }

    /**
     * @param $url
     * @return \Robo\Result
     */
    protected function _hgClone($url)
    {
        return $this->taskHgStack()->cloneRepo($url)->run();
    }
}
