<?php
namespace Robo\Task\Vcs;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        'taskSvnStack',
        'taskGitStack',
    ];

    public function register()
    {
        $this->getContainer()->add('taskSvnStack', SvnStack::class);
        $this->getContainer()->add('taskGitStack', GitStack::class);
    }
}
