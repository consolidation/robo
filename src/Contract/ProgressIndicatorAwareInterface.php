<?php
namespace Robo\Contract;

/**
 * Any Robo task that uses the Timer trait and
 * implements ProgressIndicatorAwareInterface will
 * display a progress bar while the timer is running.
 * Call advanceProgressIndicator to advance the indicator.
 *
 * Interface ProgressIndicatorAwareInterface
 * @package Robo\Contract
 */
interface ProgressIndicatorAwareInterface
{
    public function setProgressIndicator($progressIndicator);
    public function startProgressIndicator($totalSteps = 0);
    public function stopProgressIndicator();
    public function hideProgressIndicator();
    public function showProgressIndicator();
    public function advanceProgressIndicator($steps = 1);
}
