<?php
namespace Robo\Common;

trait Timer 
{
    protected $startedAt;
    protected $finishedAt;

    protected function startTimer()
    {
        if ($this->startedAt) return;
        $this->startedAt = microtime(true);
    }

    protected function stopTimer()
    {
        $this->finishedAt = microtime(true);
    }

    protected function getExecutionTime()
    {
        if ($this->finishedAt - $this->startedAt <= 0) return null;
        return $this->finishedAt-$this->startedAt;
    }
} 