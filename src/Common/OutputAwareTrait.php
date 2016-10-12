<?php

namespace Robo\Common;

use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

trait OutputAwareTrait
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @inheritdoc
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function output()
    {
        if (!isset($this->output)) {
            $this->setOutput(new NullOutput());
        }
        return $this->output;
    }

    // Backwards compatibility
    protected function getOutput()
    {
        return $this->output();
    }
}
