<?php
namespace Robo\State;

use Robo\State\Data;

interface StateAwareInterface
{
    /**
     * @return Data
     */
    public function getState();

    /**
     * @param Data state
     */
    public function setState(Data $state);

    /**
     * @param $key
     * @param value
     */
    public function setStateValue($key, $value);

    /**
     * @param Data update state takes precedence over current state.
     */
    public function updateState(Data $update);

    public function resetState();
}
