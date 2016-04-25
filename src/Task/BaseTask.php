<?php
namespace Robo\Task;

use Robo\Common\Configuration;
use Robo\Common\TaskIO;
use Robo\TaskAccessor;
use Robo\Collection\Collectable;
use Robo\Contract\TaskInterface;
use Psr\Log\LoggerAwareInterface;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;

abstract class BaseTask implements TaskInterface, LoggerAwareInterface, ContainerAwareInterface
{
    use TaskIO; // uses LoggerAwareTrait
    use TaskAccessor;

    use ContainerAwareTrait;
    use Configuration;
    use Collectable;
}
