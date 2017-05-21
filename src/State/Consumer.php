<?php
namespace Robo\State;

use Robo\ResultData;

interface Consumer
{
    /**
     * @return ResultData
     */
    public function receiveState(ResultData $state);
}
