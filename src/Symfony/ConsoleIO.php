<?php

namespace Robo\Symfony;

use Consolidation\AnnotatedCommand\CommandData;
use Consolidation\AnnotatedCommand\CommandProcessor;
use Consolidation\AnnotatedCommand\ParameterInjector;
use Robo\Common\InflectionTrait;
use Robo\Contract\InflectionInterface;
use Robo\Contract\OutputAwareInterface;
use Symfony\Component\Console\Input\InputAwareInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConsoleIO extends SymfonyStyle implements InflectionInterface // InputInterface?
{
    use InflectionTrait;

    protected $input;
    protected $output;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        parent::__construct($input, $output);
    }

    public function input()
    {
        return $this->input;
    }

    public function output()
    {
        return $this->output;
    }

    /**
     * {@inheritdoc}
     */
    public function lightText($message)
    {
        $this->block($message, '', 'fg=gray', '', true);
    }

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($child)
    {
        if ($child instanceof InputAwareInterface) {
            $child->setInput($this->input());
        }
        if ($child instanceof OutputAwareInterface) {
            $child->setOutput($this->output());
        }
    }
}
