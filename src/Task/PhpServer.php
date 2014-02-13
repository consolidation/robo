<?php
namespace Robo\Task;

/**
 * Start PHP Server and
 */
trait PhpServer
{
    protected function taskServer($port)
    {
        $server = new PhpServerTask($port);
        $this->runningCommands[] = $server;
        return $server;
    }
}

/**
 * Runs PHP server and stops it when task finishes.
 *
 * ``` php
 * <?php
 * $this->taskServer(8000)
 *  ->dir('public')
 *  ->run();
 * ?>
 * ```
 */
class PhpServerTask extends ExecTask
{
    public function __construct($port = 8000)
    {
        $this->command = "php -S 127.0.0.1:$port ";
    }

    public function dir($path)
    {
        $this->command .= "-t $path";
        return $this;
    }

} 