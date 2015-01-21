<?php
namespace Robo\Task\Docker;


/**
 * Commits docker container to an image
 *
 * ```
 * $this->taskDockerCommit($containerId)
 *      ->name('my/database')
 *      ->run();
 *
 * // alternatively you can take the result from DockerRun task:
 *
 * $result = $this->taskDockerRun('db)
 *      ->exec('./prepare_database.sh')
 *      ->run();
 *
 * $task->dockerCommit($result)
 *      ->name('my/database')
 *      ->run();
 * ```
 */
class Commit extends Base
{
    protected $command = "docker commit";
    protected $name;
    protected $cid;

    public function __construct($cidOrResult)
    {
        $this->cid = $cidOrResult instanceof Result ? $cidOrResult->getCid() : $cidOrResult;
    }

    public function getCommand()
    {
        return $this->command . ' ' . $this->cid . ' ' . $this->name . ' ' . $this->arguments;
    }

    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

}