<?php
namespace Robo\State;

use Robo\ResultData;

trait StateAwareTrait
{
    protected $state;

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function setState(ResultData $state)
    {
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function updateState(ResultData $update)
    {
        $this->state->update($update);
    }

    /**
     * {@inheritdoc}
     */
    public function resetState()
    {
        $this->state = new ResultData();
    }
}
