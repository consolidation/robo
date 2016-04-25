<?php
namespace Robo\Task\Development;

use Robo\Container\SimpleServiceProvider;

trait loadTasks
{
    /**
     * Return services.
     */
    public static function getDevelopmentServices()
    {
        return new SimpleServiceProvider(
            [
                'taskChangelog' => Changelog::class,
                'taskGenDoc' => GenerateMarkdownDoc::class,
                'taskGenTask' => GenerateTask::class,
                'taskSemVer' => SemVer::class,
                'taskServer' => PhpServer::class,
                'taskPackPhar' => PackPhar::class,
                'taskGitHubRelease' => GitHubRelease::class,
                'taskOpenBrowser' => OpenBrowser::class,
            ]
        );
    }

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
     * @param $filename
     * @return GenerateMarkdownDoc
     */
    protected function taskGenTask($className, $wrapperClassName = '')
    {
        return $this->task(__FUNCTION__, $className, $wrapperClassName);
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
