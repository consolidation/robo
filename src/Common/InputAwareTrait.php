<?php

namespace Robo\Common;

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
    public function input()
    {
        return $this->input;
    }
}
