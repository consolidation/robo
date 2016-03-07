<?php
namespace Robo;

use Robo\Common\IO;

class Tasks
{
    use Tasklib;
    use IO;

    protected function stopOnFail($stopOnFail = true)
    {
        Result::$stopOnFail = $stopOnFail;
    }
}
