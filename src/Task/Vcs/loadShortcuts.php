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
        return (new SvnStack())->checkout($url)->run();
    }

    /**
     * @param $url
     * @return \Robo\Result
     */
    protected function _gitClone($url)
    {
        return (new GitStack())->cloneRepo($url)->run();
    }
} 