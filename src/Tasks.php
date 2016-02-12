<?php
namespace Robo;

class Tasks
{
    use Tasklib;
    use Common\IO;

    protected function stopOnFail($stopOnFail = true)
    {
        Result::$stopOnFail = $stopOnFail;
    }
}
