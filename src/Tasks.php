<?php

namespace Robo;

use League\Container\ContainerAwareInterface;
use Robo\Common\ContainerAwareTrait;
use Robo\Common\IO;
use Robo\Contract\BuilderAwareInterface;
use Robo\Contract\IOAwareInterface;

class Tasks implements BuilderAwareInterface, IOAwareInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;
    use LoadAllTasks; // uses TaskAccessor, which uses BuilderAwareTrait
    use IO;

    /**
     * @param bool $stopOnFail
     */
    protected function stopOnFail($stopOnFail = true)
    {
        Result::$stopOnFail = $stopOnFail;
    }
}
