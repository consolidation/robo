<?php
namespace Robo\Task\Development;

trait loadTasks
{
    /**
     * @param string $filename
     * @return Changelog
     */
    protected function taskChangelog($filename = 'CHANGELOG.md')
    {
        return $this->task(__FUNCTION__, $filename);
    }

    /**
     * @param $filename
     * @return GenerateMarkdownDoc
     */
    protected function taskGenDoc($filename)
    {
        return $this->task(__FUNCTION__, $filename);
    }

    /**
     * @param string $pathToSemVer
     * @return SemVer
     */
     protected function taskSemVer($pathToSemVer = '.semver')
     {
         return $this->task(__FUNCTION__, $pathToSemVer);
     }

    /**
     * @param int $port
     * @return PhpServer
     */
    protected function taskServer($port = 8000)
    {
        return $this->task(__FUNCTION__, $port);
    }

    /**
     * @param $filename
     * @return PackPhar
     */
    protected function taskPackPhar($filename)
    {
        return $this->task(__FUNCTION__, $filename);
    }

    /**
     * @param $tag
     * @return GitHubRelease
     */
    protected function taskGitHubRelease($tag)
    {
        return $this->task(__FUNCTION__, $tag);
    }

    /**
     * @param string|array $url
     * @return OpenBrowser
     */
    protected function taskOpenBrowser($url)
    {
        return $this->task(__FUNCTION__, $url);
    }
}
