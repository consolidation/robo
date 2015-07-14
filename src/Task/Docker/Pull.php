<?php
namespace Robo\Task\Docker;

/**
 * Pulls an image from DockerHub
 *
 * ```php
 * <?php
 * $this->taskDockerPull('wordpress')
 *      ->run();
 *
 * ?>
 * ```
 *
 */
class Pull extends Base
{
    function __construct($image)
    {
        $this->command = "docker pull $image ";
    }

    public function getCommand()
    {
        return $this->command . ' ' . $this->arguments;
    }

}