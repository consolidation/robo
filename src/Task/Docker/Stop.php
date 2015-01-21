<?php
namespace Robo\Task\Docker;

/**
 * Stops Docker container
 *
 * ```php
 * <?php
 * $this->taskDockerStop($cidOrResult)
 *      ->run();
 * ?>
 * ```
 */
class Stop extends Base
{
    protected $command = "docker stop";
    protected $cid;

    public function __construct($cidOrResult)
    {
        $this->cid = $cidOrResult instanceof Result ? $cidOrResult->getCid() : $cidOrResult;
    }

    public function getCommand()
    {
        return $this->command . ' ' . $this->arguments . ' ' . $this->cid;
    }
}