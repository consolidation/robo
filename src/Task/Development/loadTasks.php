<?php
namespace Robo\Task\Development;

trait loadTasks
{
    /**
     * @param string $filename
     *
     * @return Changelog
     */
    protected function taskChangelog($filename = 'CHANGELOG.md')
    {
        return $this->task(Changelog::class, $filename);
    }

    /**
     * @param string $filename
     *
     * @return GenerateMarkdownDoc
     */
    protected function taskGenDoc($filename)
    {
        return $this->task(GenerateMarkdownDoc::class, $filename);
    }

    /**
     * @param string $className
     * @param string $wrapperClassName
     *
     * @return \Robo\Task\Development\GenerateTask
     */
    protected function taskGenTask($className, $wrapperClassName = '')
    {
        return $this->task(GenerateTask::class, $className, $wrapperClassName);
    }

    /**
     * @param string $pathToSemVer
     *
     * @return SemVer
     */
    protected function taskSemVer($pathToSemVer = '.semver')
    {
        return $this->task(SemVer::class, $pathToSemVer);
    }

    /**
     * @param int $port
     *
     * @return PhpServer
     */
    protected function taskServer($port = 8000)
    {
        return $this->task(PhpServer::class, $port);
    }

    /**
     * @param string $filename
     *
     * @return PackPhar
     */
    protected function taskPackPhar($filename)
    {
        return $this->task(PackPhar::class, $filename);
    }

    /**
     * @param string $tag
     *
     * @return GitHubRelease
     */
    protected function taskGitHubRelease($tag)
    {
        return $this->task(GitHubRelease::class, $tag);
    }

    /**
     * @param string|array $url
     *
     * @return OpenBrowser
     */
    protected function taskOpenBrowser($url)
    {
        return $this->task(OpenBrowser::class, $url);
    }
}
