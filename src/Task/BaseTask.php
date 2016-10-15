<?php
namespace Robo\Task;

use Robo\Common\InflectionTrait;
use Robo\Contract\InflectionInterface;

use Robo\Common\TaskIO;
use Robo\Contract\TaskInterface;
use Robo\Contract\ProgressIndicatorAwareInterface;
use Robo\Common\ProgressIndicatorAwareTrait;
use Robo\Contract\ConfigAwareInterface;
use Psr\Log\LoggerAwareInterface;

abstract class BaseTask implements TaskInterface, LoggerAwareInterface, ConfigAwareInterface, ProgressIndicatorAwareInterface, InflectionInterface
{
    use TaskIO; // uses LoggerAwareTrait and ConfigAwareTrait
    use ProgressIndicatorAwareTrait;
    use InflectionTrait;

    /**
     * {@inheritdoc}
     */
    public function injectDependencies(InflectionInterface $child)
    {
        if ($child instanceof LoggerAwareInterface && $this->logger) {
            $child->setLogger($this->logger);
        }
        if ($child instanceof ProgressIndicatorAwareInterface && $this->progressIndicator) {
            $child->setProgressIndicator($this->progressIndicator);
        }
        if ($child instanceof ConfigAwareInterface && $this->getConfig()) {
            $child->setConfig($this->getConfig());
        }
    }
}
