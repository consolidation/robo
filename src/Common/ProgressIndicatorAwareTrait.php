<?php
namespace Robo\Common;

trait ProgressIndicatorAwareTrait
{
    use Timer;

    protected $progressIndicator;
    protected $progressIndicatorRunning = false;

    public function setProgressIndicator($progressIndicator)
    {
        $this->progressIndicator = $progressIndicator;
    }

    public function hideProgressIndicator()
    {
        if ($this->progressIndicatorRunning) {
            $this->progressIndicator->clear();
            // Hack: progress indicator does not reset cursor to beginning of line on 'clear'
            \Robo\Config::output()->write("\x0D");
        }
    }

    public function showProgressIndicator()
    {
        if ($this->progressIndicatorRunning) {
            $this->progressIndicator->display();
        }
    }

    public function startProgressIndicator($totalSteps = 0)
    {
        $this->startTimer();
        if (isset($this->progressIndicator)) {
            $this->progressIndicator->start($totalSteps);
            $this->progressIndicator->display();
            $this->progressIndicatorRunning = true;
        }
    }

    public function stopProgressIndicator()
    {
        $this->stopTimer();
        if ($this->progressIndicatorRunning) {
            $this->progressIndicatorRunning = false;
            $this->progressIndicator->finish();
            // Hack: progress indicator does not always finish cleanly
            \Robo\Config::output()->writeln('');
        }
    }

    public function advanceProgressIndicator($steps = 1)
    {
        if ($this->progressIndicatorRunning) {
            $this->progressIndicator->advance($steps);
        }
    }
}
