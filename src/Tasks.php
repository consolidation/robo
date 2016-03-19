<?php
namespace Robo;

use Robo\Common\IO;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;

class Tasks implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use Tasklib;
    use IO;

    protected function stopOnFail($stopOnFail = true)
    {
        Result::$stopOnFail = $stopOnFail;
    }
}
