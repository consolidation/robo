<?php
namespace Robo\Task\ApiGen;

use Robo\Container\SimpleServiceProvider;

class ServiceProvider extends SimpleServiceProvider
{
    public function __construct()
    {
        parent::__construct(
            [
                'taskApiGen' => ApiGen::class,
            ]
        );
    }
}
