<?php
namespace Robo\Task\Docker;

/**
 * Builds Docker image
 *
 * ```php
 * <?php
 * $this->taskDockerBuild()->run();
 *
 * $this->dockerBuild('path/to/dir')
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
    protected $path;

    public function __construct($path = '.')
    {
        $this->command = "docker build";
        $this->path = $path;
    }

    public function getCommand()
    {
        return $this->command . ' ' . $this->arguments . ' ' . $this->path;
    }

    public function tag($tag)
    {
        return $this->option('-t', $tag);
    }

}