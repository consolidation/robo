<?php
namespace Robo\Common;

class TimeKeeper
{
    protected $startedAt;
    protected $finishedAt;

    public function start()
    {
        if ($this->startedAt) {
            return;
        }
        // Get time in seconds as a float, accurate to the microsecond.
        $this->startedAt = microtime(true);
    }

    public function stop()
    {
        $this->finishedAt = microtime(true);
    }

    public function elapsed()
    {
        $finished = $this->finishedAt ? $this->finishedAt : microtime(true);
        if ($finished - $this->startedAt <= 0) {
            return null;
        }
        return $finished - $this->startedAt;
    }
}
