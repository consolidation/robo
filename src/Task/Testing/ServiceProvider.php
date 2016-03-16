<?php
namespace Robo\Task\Testing;

use Robo\Container\SimpleServiceProvider;

class ServiceProvider extends SimpleServiceProvider
{
    public function __construct()
    {
        parent::__construct(
            [
                'taskCodecept' => Codecept::class,
                'taskPHPUnit' => PHPUnit::class,
                'taskPhpspec' => Phpspec::class,
            ]
        );
    }
}
