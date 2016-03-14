<?php
namespace Robo\Task\Testing;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        'taskCodecept',
        'taskPHPUnit',
        'taskPhpspec',
    ];

    public function register()
    {
        $this->getContainer()->add('taskCodecept', Codecept::class);
        $this->getContainer()->add('taskPHPUnit', PHPUnit::class);
        $this->getContainer()->add('taskPhpspec', Phpspec::class);
    }
}
