<?php
namespace Robo\Common;

trait Timer
{
    /** var TimeKeeper */
    protected $timer;

    protected function startTimer()
    {
        if (!isset($this->timer)) {
            $this->timer = new TimeKeeper();
        }
        $this->timer->start();
    }

    protected function stopTimer()
    {
        if (!isset($this->timer)) {
            return;
        }
        $this->timer->stop();
    }

    protected function getExecutionTime()
    {
        if (!isset($this->timer)) {
            return null;
        }
        return $this->timer->elapsed();
    }
}
