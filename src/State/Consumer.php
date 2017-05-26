<?php
namespace Robo\State;

use Robo\State\Data;

interface Consumer
{
    /**
     * @return Data
     */
    public function receiveState(Data $state);
}
