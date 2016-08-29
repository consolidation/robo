<?php
namespace Robo\Common;

trait ProgressIndicatorAwareTrait
{
    use Timer;
    protected $progressIndicator;

    public function progressIndicatorSteps()
    {
        return 0;
    }

    public function setProgressIndicator($progressIndicator)
    {
        $this->progressIndicator = $progressIndicator;
    }

    protected function hideProgressIndicator()
    {
        if (!$this->progressIndicator) {
            return;
        }
        return $this->progressIndicator->hideProgressIndicator();
    }

    protected function showProgressIndicator()
    {
        if (!$this->progressIndicator) {
            return;
        }
        $this->progressIndicator->showProgressIndicator();
    }

    protected function restoreProgressIndicator($visible)
    {
        if (!$this->progressIndicator) {
            return;
        }
        $this->progressIndicator->restoreProgressIndicator($visible);
    }

    protected function getTotalExecutionTime()
    {
        if (!$this->progressIndicator) {
            return 0;
        }
        $this->progressIndicator->getExecutionTime();
    }

    protected function startProgressIndicator()
    {
        $this->startTimer();
        if (!$this->progressIndicator) {
            return;
        }
        $totalSteps = $this->progressIndicatorSteps();
        $this->progressIndicator->startProgressIndicator($totalSteps, $this);
    }

    protected function inProgress()
    {
        if (!$this->progressIndicator) {
            return false;
        }
        return $this->progressIndicator->inProgress();
    }

    protected function stopProgressIndicator()
    {
        $this->stopTimer();
        if (!$this->progressIndicator) {
            return;
        }
        $this->progressIndicator->stopProgressIndicator($this);
    }

    protected function disableProgressIndicator()
    {
        $this->stopTimer();
        if (!$this->progressIndicator) {
            return;
        }
        $this->progressIndicator->disableProgressIndicator();
    }

    protected function detatchProgressIndicator()
    {
        $this->setProgressIndicator(null);
    }

    protected function advanceProgressIndicator($steps = 1)
    {
        if (!$this->progressIndicator) {
            return;
        }
        $this->progressIndicator->advanceProgressIndicator($steps);
    }
}
