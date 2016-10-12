<?php

namespace Robo\Common;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;

trait InputAwareTrait
{
    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * @inheritdoc
     */
    public function setInput(InputInterface $input)
    {
        $this->input = $input;

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function input()
    {
        if (!isset($this->input)) {
            $this->setInput(new ArgvInput());
        }
        return $this->input;
    }

    // Backwards compatibility.
    protected function getInput()
    {
        return $this->input();
    }
}
