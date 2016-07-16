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
        return $this->getContainer()->get('taskSvnStack')->checkout($url)->run();
    }

    /**
     * @param $url
     * @return \Robo\Result
     */
    protected function _gitClone($url)
    {
        return $this->getContainer()->get('taskGitStack')->cloneRepo($url)->run();
    }

    /**
     * @param $url
     * @return \Robo\Result
     */
    protected function _hgClone($url)
    {
        return $this->getContainer()->get('taskHgStack')->cloneRepo($url)->run();
    }
}
