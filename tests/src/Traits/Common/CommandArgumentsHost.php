<?php

namespace Robo\Traits\Common;

use Robo\Common\CommandArguments;

class CommandArgumentsHost
{
    use CommandArguments;

    /**
     * @return string
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}
