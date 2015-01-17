<?php
namespace Robo\Task\Docker;

use Robo\Common\CommandReceiver;

class Run extends Base
{
    use CommandReceiver;

    protected $image = '';
    protected $run = '';
    protected $cidFile;

    function __construct($image)
    {
        $this->image = $image;
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

    public function exec($run)
    {
        $this->run = $this->retrieveCommand($run);
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

    public function publish($port = null)
    {
        if (!$port) {
            return $this->option('-P');
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
        return $this->option('name', $name);
    }

    public function run()
    {
        $this->cidFile = sys_get_temp_dir() . '/docker_' . uniqid() . '.cid';
        $result = parent::run();
        $cid = $this->getCid();
        return new Result($this, $result->getExitCode(), $result->getMessage(), ['cid' => $cid]);
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