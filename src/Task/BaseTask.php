<?php
namespace Robo\Task;

use Robo\Common\Configuration;
use Robo\Common\TaskIO;
use Robo\Collection\Collectable;
use Robo\Contract\TaskInterface;
use Psr\Log\LoggerAwareInterface;

abstract class BaseTask implements TaskInterface, LoggerAwareInterface
{
    use TaskIO; // uses LoggerAwareTrait
    use Configuration;
    use Collectable;
}
