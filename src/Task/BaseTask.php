<?php
namespace Robo\Task;

use Robo\Common\InflectionTrait;
use Robo\Contract\InflectionInterface;

use Robo\Common\Configuration;
use Robo\Common\TaskIO;
use Robo\Collection\Collectable;
use Robo\Contract\TaskInterface;
use Robo\Contract\ProgressIndicatorAwareInterface;
use Robo\Common\ProgressIndicatorAwareTrait;
use Psr\Log\LoggerAwareInterface;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;

abstract class BaseTask implements TaskInterface, LoggerAwareInterface, ProgressIndicatorAwareInterface, InflectionInterface
{
    use TaskIO; // uses LoggerAwareTrait
    use ProgressIndicatorAwareTrait;
    use InflectionTrait;

    use Configuration;
    use Collectable;

    /**
     * {inheritdoc}
     */
    public function injectDependencies(InflectionInterface $child)
    {
        if ($child instanceof LoggerAwareInterface) {
            $child->setLogger($this->logger);
        }
        if ($child instanceof ProgressIndicatorAwareInterface) {
            $child->setProgressIndicator($this->progressIndicator);
        }
    }
}
