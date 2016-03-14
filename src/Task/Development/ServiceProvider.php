<?php
namespace Robo\Task\Development;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        'taskChangelog',
        'taskGenDoc',
        'taskSemVer',
        'taskServer',
        'taskPackPhar',
        'taskGitHubRelease',
        'taskOpenBrowser',
    ];

    public function register()
    {
        $this->getContainer()->add('taskChangelog', Changelog::class);
        $this->getContainer()->add('taskGenDoc', GenerateMarkdownDoc::class);
        $this->getContainer()->add('taskSemVer', SemVer::class);
        $this->getContainer()->add('taskServer', PhpServer::class);
        $this->getContainer()->add('taskPackPhar', PackPhar::class);
        $this->getContainer()->add('taskGitHubRelease', DumpAutoload::class);
        $this->getContainer()->add('taskOpenBrowser', OpenBrowser::class);
    }
}
