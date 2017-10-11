<?php
namespace Robo\State;

use Robo\State\Data;

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
    public function setState(Data $state)
    {
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function setStateValue($key, $value)
    {
        $this->state[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function updateState(Data $update)
    {
        $this->state->update($update);
    }

    /**
     * {@inheritdoc}
     */
    public function resetState()
    {
        $this->state = new Data();
    }
}
