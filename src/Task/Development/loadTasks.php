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
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Development\Changelog',
            [$filename]
        );
    }

    /**
     * @param $filename
     * @return GenerateMarkdownDoc
     */
    protected function taskGenDoc($filename)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Development\GenerateMarkdownDoc',
            [$filename]
        );
    }

    /**
     * @param string $pathToSemVer
     * @return SemVer
     */
     protected function taskSemVer($pathToSemVer = '.semver')
     {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Development\SemVer',
            [$pathToSemVer]
        );
     }

    /**
     * @param int $port
     * @return PhpServer
     */
    protected function taskServer($port = 8000)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Development\PhpServer',
            [$port]
        );
    }

    /**
     * @param $filename
     * @return PackPhar
     */
    protected function taskPackPhar($filename)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Development\PackPhar',
            [$filename]
        );
    }

    /**
     * @param $tag
     * @return GitHubRelease
     */
    protected function taskGitHubRelease($tag)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Development\GitHubRelease',
            [$tag]
        );
    }

    /**
     * @param string|array $url
     * @return OpenBrowser
     */
    protected function taskOpenBrowser($url)
    {
        return $this->taskAssembler()->assemble(
            '\Robo\Task\Development\OpenBrowser',
            [$url]
        );
    }
}
