<?php
namespace Robo\Task\Development;

use Robo\Task\Base\Exec;

/**
 * Runs PHP server and stops it when task finishes.
 *
 * ``` php
 * <?php
 * // run server in /public directory
 * $this->taskPhpServer(8000)
 *  ->dir('public')
 *  ->run();
 *
 * // run with IP 0.0.0.0
 * $this->taskPhpServer(8000)
 *  ->host('0.0.0.0')
 *  ->run();
 *
 * // execute server in background
 * $this->taskPhpServer(8000)
 *  ->background()
 *  ->run();
 * ?>
 * ```
 */
class PhpServer extends Exec
{
    protected $port;
    protected $host = '127.0.0.1';
    protected $command = 'php -S %s:%d ';

    public function __construct($port)
    {
        $this->port = $port;
    }

    public function host($host)
    {
        $this->host = $host;
        return $this;
    }

    public function dir($path)
    {
        $this->command .= "-t $path";
        return $this;
    }

    public function getCommand()
    {
        return sprintf($this->command . $this->arguments, $this->host, $this->port);
    }

}