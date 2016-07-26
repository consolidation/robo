<?php
namespace Robo\Task;

use Robo\Common\InflectionTrait;
use Robo\Contract\InflectionInterface;

use Robo\Common\TaskIO;
use Robo\Collection\Collectable;
use Robo\Contract\TaskInterface;
use Robo\Contract\ProgressIndicatorAwareInterface;
use Robo\Common\ProgressIndicatorAwareTrait;
use Robo\Contract\ConfigAwareInterface;
use Robo\Common\ConfigAwareTrait;
use Psr\Log\LoggerAwareInterface;

// TODO: Ensure that ConfigAwareInterface is only used for global options; then, add it only to tasks that need it.
abstract class BaseTask implements TaskInterface, LoggerAwareInterface, ProgressIndicatorAwareInterface, ConfigAwareInterface, InflectionInterface
{
    use TaskIO; // uses LoggerAwareTrait
    use ProgressIndicatorAwareTrait;
    use ConfigAwareTrait;
    use InflectionTrait;

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
        if ($child instanceof ConfigAwareInterface) {
            $child->setConfig($this->getConfig());
        }
    }
}
