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
        return new Changelog($filename);
    }

    /**
     * @param $filename
     * @return GenerateMarkdownDoc
     */
    protected function taskGenDoc($filename)
    {
        return new GenerateMarkdownDoc($filename);
    }

    /**
     * @param string $pathToSemVer
     * @return SemVer
     */
     protected function taskSemVer($pathToSemVer = '.semver')
     {
         return new SemVer($pathToSemVer);
     }

    /**
     * @param int $port
     * @return PhpServer
     */
    protected function taskServer($port = 8000)
    {
        return new PhpServer($port);
    }

    /**
     * @param $filename
     * @return PackPhar
     */
    protected function taskPackPhar($filename)
    {
        return new PackPhar($filename);
    }

    /**
     * @param $tag
     * @return GitHubRelease
     */
    protected function taskGitHubRelease($tag)
    {
        return new GitHubRelease($tag);
    }
} 