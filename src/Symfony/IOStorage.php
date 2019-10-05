<?php
namespace Robo\Symfony;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * IOStorage insures that a single common style object is
 * used by all IOAware classes.
 */
class IOStorage
{
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    protected $io;

    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @var string
     */
    protected $styleClass = '\Symfony\Component\Console\Style\SymfonyStyle';

    public function clear()
    {
        $this->input = null;
        $this->output = null;
        $this->io = null;
    }

    /**
     * setStyleClass sets a new style class to use.
     *
     * @param string $styleClass Name of class to instantiate when style object
     *   is requested. Must be a subclass of SymfonyStyle.
     */
    public function setStyleClass($styleClass)
    {
        $this->styleClass = $styleClass;
        $this->recreate();
    }

    /**
     * hasStyle indicates whether there is a cached style available
     *
     * @return bool
     */
    public function hasStyle()
    {
        return !empty($this->io);
    }

    /**
     * create will instantiate a new style instance, replacing what was
     * there before.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return SymfonyStyle
     */
    public function create(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        return $this->instantiate();
    }

    /**
     * recreate will make a new style object iff we have cached $input
     * and $output objects. Otherwise it clears the cached style object.
     */
    protected function recreate()
    {
        if (!empty($this->input) && !empty($this->output)) {
            return $this->instantiate();
        }
        $this->io = null;
        return null;
    }

    /**
     * instantiate will make a new style object from the cached input and
     * output objects.
     */
    protected function instantiate()
    {
        $this->io = new $this->styleClass($this->input, $this->output);
        return $this->cached();
    }

    /**
     * get will return the cached style object, if it exists; otherwise,
     * it will create and cache a new style object using the provided
     * input and output objects.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return SymfonyStyle
     */
    public function get(InputInterface $input, OutputInterface $output)
    {
        if (!$this->hasStyle()) {
            $this->create($input, $output);
        }
        return $this->cached();
    }

    /**
     * cached returns the cached style object, or null if none is available.
     *
     * @return SymfonyStyle|null
     */
    public function cached()
    {
        return $this->io;
    }

    /**
     * Return our stored InputInterface object.
     *
     * @return InputInterface
     */
    public function input()
    {
        return $this->input;
    }

    /**
     * Return our stored OutputInterface object.
     *
     * @return OutputInterface
     */
    public function output()
    {
        return $this->output;
    }
}
