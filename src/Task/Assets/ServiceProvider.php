<?php
namespace Robo\Task\Assets;

use Robo\Container\SimpleServiceProvider;

class ServiceProvider extends SimpleServiceProvider
{
    public function __construct()
    {
        parent::__construct(
            [
                'taskMinify' => Minify::class,
                'taskImageMinify' => ImageMinify::class,
                'taskLess' => Less::class,
                'taskScss' => Scss::class,
            ]
        );
    }
}
