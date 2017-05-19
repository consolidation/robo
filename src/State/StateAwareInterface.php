<?php
namespace Robo\State;

use Robo\ResultData;

interface StateAwareInterface
{
    /**
     * @return ResultData
     */
    public function getState();

    /**
     * @param ResultData state
     */
    public function setState(ResultData $state);

    /**
     * @param ResultData update state takes precedence over current state.
     */
    public function updateState(ResultData $update);

    public function resetState();
}
