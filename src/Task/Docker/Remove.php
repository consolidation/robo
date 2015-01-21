<?php
namespace Robo\Task\Docker;

/**
 * Remove docker container
 *
 * ```php
 * <?php
 * $this->taskDockerRemove($container)
 *      ->run();
 * ?>
 * ```
 *
 */
class Remove extends Base
{
    function __construct($container)
    {
        $this->command = "docker rm $container ";
    }
}