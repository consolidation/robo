<?php
namespace Robo\Task\Development;

use Robo\Container\SimpleServiceProvider;

class ServiceProvider extends SimpleServiceProvider
{
    public function __construct()
    {
        parent::__construct(
            [
                'taskChangelog' => Changelog::class,
                'taskGenDoc' => GenerateMarkdownDoc::class,
                'taskSemVer' => SemVer::class,
                'taskServer' => PhpServer::class,
                'taskPackPhar' => PackPhar::class,
                'taskGitHubRelease' => GitHubRelease::class,
                'taskOpenBrowser' => OpenBrowser::class,
            ]
        );
    }
}
