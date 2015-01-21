<?php
namespace Robo\Task\Docker;

use Robo\Common\CommandReceiver;

/**
 * Performs `docker run` on a container.
 *
 * ```php
 * <?php
 * $this->taskDockerRun('mysql')->run();
 *
 * $result = $this->taskDockerRun('my_db_image')
 *      ->env('DB', 'database_name')
 *      ->volume('/path/to/data', '/data')
 *      ->detached()
 *      ->publish(3306)
 *      ->name('my_mysql')
 *      ->run();
 *
 * // retrieve container's cid:
 * $this->say("Running container ".$result->getCid());
 *
 * // execute script inside container
 * $result = $this->taskDockerRun('db')
 *      ->exec('prepare_test_data.sh')
 *      ->run();
 *
 * $this->taskDockerCommit($result)
 *      ->name('test_db')
 *      ->run();
 *
 * // link containers
 * $mysql = $this->taskDockerRun('mysql')
 *      ->name('wp_db') // important to set name for linked container
 *      ->env('MYSQL_ROOT_PASSWORD', '123456')
 *      ->run();
 *
 * $this->taskDockerRun('wordpress')
 *      ->link($mysql)
 *      ->publish(80, 8080)
 *      ->detached()
 *      ->run();
 *
 * ?>
 * ```
 *
 */
class Run extends Base
{
    use CommandReceiver;

    protected $image = '';
    protected $run = '';
    protected $cidFile;
    protected $name;

    function __construct($image)
    {
        $this->image = $image;
    }

    public function getPrinted()
    {
        return $this->isPrinted;
    }

    public function getCommand()
    {
        if ($this->isPrinted) {
            $this->option('-i');
        }
        if ($this->cidFile) {
            $this->option('cidfile', $this->cidFile);
        }
        return trim('docker run ' . $this->arguments . ' ' . $this->image . ' ' . $this->run);
    }

    public function detached()
    {
        $this->option('-d');
        return $this;
    }

    public function interactive()
    {
        $this->option('-i');
        return $this;
    }

    public function exec($run)
    {
        $this->run = $this->receiveCommand($run);
        return $this;
    }

    public function volume($from, $to = null)
    {
        $volume = $to ? "$from:$to" : $from;
        $this->option('-v', $volume);
        return $this;
    }

    public function env($variable, $value = null)
    {
        $env = $value ? "$variable=$value" : $variable;
        return $this->option("-e", $env);
    }

    public function publish($port = null, $portTo = null)
    {
        if (!$port) {
            return $this->option('-P');
        }
        if ($portTo) {
            $port = "$port:$portTo";
        }
        return $this->option('-p', $port);
    }

    public function containerWorkdir($dir)
    {
        return $this->option('-w', $dir);
    }

    public function user($user)
    {
        return $this->option('-u', $user);
    }

    public function privileged()
    {
        return $this->option('--privileged');
    }

    public function name($name)
    {
        $this->name = $name;
        return $this->option('name', $name);
    }

    public function link($name, $alias)
    {
        if ($name instanceof Result) {
            $name = $name->getContainerName();
        }
        $this->option('link', "$name:$alias");
        return $this;
    }

    public function run()
    {
        $this->cidFile = sys_get_temp_dir() . '/docker_' . uniqid() . '.cid';
        $result = parent::run();
        $time = $result->getExecutionTime();
        $cid = $this->getCid();
        return new Result($this, $result->getExitCode(), $result->getMessage(), ['cid' => $cid, 'time' => $time, 'name' => $this->name]);
    }

    protected function getCid()
    {
        if (!$this->cidFile) {
            return null;
        }
        $cid = trim(file_get_contents($this->cidFile));
        @unlink($this->cidFile);
        return $cid;
    }
}