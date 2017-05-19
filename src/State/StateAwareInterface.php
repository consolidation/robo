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

    public function resetState();
}
