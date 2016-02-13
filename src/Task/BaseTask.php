<?php
namespace Robo\Task;

use Robo\Common\Configuration;
use Robo\Common\TaskIO;
use Robo\Collection\Collectable;
use Robo\Contract\TaskInterface;

abstract class BaseTask implements TaskInterface
{
    use TaskIO;
    use Configuration;
    use Collectable;
}
