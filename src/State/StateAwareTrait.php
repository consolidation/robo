<?php
namespace Robo\State;

use Robo\ResultData;

trait StateAwareTrait
{
    /**
     * @return ResultData
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param ResultData state
     */
    public function setState(ResultData $state)
    {
        $this->state = $state;
    }

    public function resetState()
    {
        $this->state = new ResultData();
    }
}
