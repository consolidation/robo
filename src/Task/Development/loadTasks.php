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
        return $this->task('Changelog', $filename);
    }

    /**
     * @param $filename
     * @return GenerateMarkdownDoc
     */
    protected function taskGenDoc($filename)
    {
        return $this->task('GenDoc', $filename);
    }

    /**
     * @param string $pathToSemVer
     * @return SemVer
     */
     protected function taskSemVer($pathToSemVer = '.semver')
     {
         return $this->task('SemVer', $pathToSemVer);
     }

    /**
     * @param int $port
     * @return PhpServer
     */
    protected function taskServer($port = 8000)
    {
        return $this->task('Server', $port);
    }

    /**
     * @param $filename
     * @return PackPhar
     */
    protected function taskPackPhar($filename)
    {
        return $this->task('PackPhar', $filename);
    }

    /**
     * @param $tag
     * @return GitHubRelease
     */
    protected function taskGitHubRelease($tag)
    {
        return $this->task('GitHubRelease', $tag);
    }

    /**
     * @param string|array $url
     * @return OpenBrowser
     */
    protected function taskOpenBrowser($url)
    {
        return $this->task('OpenBrowser', $url);
    }
}
