<?php

namespace Robo\Task\Docker;

/**
 * Builds Docker image
 *
 * ```php
 * <?php
 * $this->taskDockerBuild()->run();
 *
 * $this->taskDockerBuild('path/to/dir')
 *      ->tag('database')
 *      ->run();
 *
 * ?>
 *
 * ```
 *
 * Class Build
 * @package Robo\Task\Docker
 */
class Build extends Base
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @param string $path
     */
    public function __construct($path = '.')
    {
        $this->command = "docker build";
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand()
    {
        return $this->command . ' ' . $this->arguments . ' ' . $this->path;
    }

    /**
     * @param string $tag
     *
     * @return $this
     */
    public function tag($tag)
    {
        return $this->option('-t', $tag);
    }
}
