<?php
namespace Robo;

use Robo\Common\IO;
use Robo\Contract\IOAwareInterface;
use Robo\Contract\BuilderAwareInterface;

class Tasks implements BuilderAwareInterface, IOAwareInterface
{
    use LoadAllTasks; // uses TaskAccessor, which uses BuilderAwareTrait
    use IO;

    protected function stopOnFail($stopOnFail = true)
    {
        Result::$stopOnFail = $stopOnFail;
    }
}
