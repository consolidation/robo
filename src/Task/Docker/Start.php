<?php
namespace Robo\Task\Docker;

/**
 * Starts Docker container
 *
 * ```php
 * <?php
 * $this->taskDockerStart($cidOrResult)
 *      ->run();
 * ?>
 * ```
 */
class Start extends Base
{
    protected $command = "docker start";
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