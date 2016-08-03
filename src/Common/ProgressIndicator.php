<?php
namespace Robo\Common;

/**
 * Wrapper around \Symfony\Component\Console\Helper\ProgressBar
 */
class ProgressIndicator
{
    use Timer;

    /** var \Symfony\Component\Console\Helper\ProgressBar */
    protected $progressBar;
    protected $output;
    protected $progressIndicatorRunning = false;
    protected $autoDisplayInterval = 0;
    protected $cachedSteps = 0;
    protected $totalSteps = 0;
    protected $progressBarDisplayed = false;
    protected $owner;

    public function __construct($progressBar, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $this->progressBar = $progressBar;
        $this->output = $output;
    }

    public function setProgressBarAutoDisplayInterval($interval)
    {
        if ($this->progressIndicatorRunning) {
            return;
        }
        $this->autoDisplayInterval = $interval;
    }

    public function hideProgressIndicator()
    {
        $result = $this->progressBarDisplayed;
        if ($this->progressIndicatorRunning && $this->progressBarDisplayed) {
            $this->progressBar->clear();
            // Hack: progress indicator does not reset cursor to beginning of line on 'clear'
            $this->output->write("\x0D");
            $this->progressBarDisplayed = false;
        }
        return $result;
    }

    public function showProgressIndicator()
    {
        if ($this->progressIndicatorRunning && !$this->progressBarDisplayed && isset($this->progressBar)) {
            $this->progressBar->display();
            $this->progressBarDisplayed = true;
            $this->advanceProgressIndicatorCachedSteps();
        }
    }

    public function restoreProgressIndicator($visible)
    {
        if ($visible) {
            $this->showProgressIndicator();
        }
    }

    public function startProgressIndicator($totalSteps, $owner)
    {
        if (!isset($this->progressBar)) {
            return;
        }

        $this->progressIndicatorRunning = true;
        if (!isset($this->owner)) {
            $this->owner = $owner;
            $this->startTimer();
            $this->totalSteps = $totalSteps;
            $this->autoShowProgressIndicator();
        }
    }

    public function autoShowProgressIndicator()
    {
        if (($this->autoDisplayInterval < 0) || !isset($this->progressBar) || !$this->output->isDecorated()) {
            return;
        }
        if ($this->autoDisplayInterval <= $this->getExecutionTime()) {
            $this->autoDisplayInterval = -1;
            $this->progressBar->start($this->totalSteps);
            $this->showProgressIndicator();
        }
    }

    public function inProgress()
    {
        return $this->progressIndicatorRunning;
    }

    public function stopProgressIndicator($owner)
    {
        if ($this->progressIndicatorRunning && ($this->owner === $owner)) {
            $this->cleanup();
        }
    }

    protected function cleanup()
    {
        $this->progressIndicatorRunning = false;
        $this->owner = null;
        if ($this->progressBarDisplayed) {
            $this->progressBar->finish();
            // Hack: progress indicator does not always finish cleanly
            $this->output->writeln('');
            $this->progressBarDisplayed = false;
        }
        $this->stopTimer();
    }

    /**
     * Erase progress indicator and ensure it never returns.  Used
     * only during error handlers.
     */
    public function disableProgressIndicator()
    {
        $this->cleanup();
        // ProgressIndicator is shared, so this permanently removes
        // the program's ability to display progress bars.
        $this->progressBar = null;
    }

    public function advanceProgressIndicator($steps = 1)
    {
        $this->cachedSteps += $steps;
        if ($this->progressIndicatorRunning) {
            $this->autoShowProgressIndicator();
            // We only want to call `advance` if the progress bar is visible,
            // because it always displays itself when it is advanced.
            if ($this->progressBarDisplayed) {
                return $this->advanceProgressIndicatorCachedSteps();
            }
        }
    }

    protected function advanceProgressIndicatorCachedSteps()
    {
        $this->progressBar->advance($this->cachedSteps);
        $this->cachedSteps = 0;
    }
}
