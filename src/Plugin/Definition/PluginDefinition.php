<?php

namespace Robo\Plugin\Definition;

/**
 * Robo plugin definition class.
 */
class PluginDefinition implements PluginDefinitionInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $file;

    /**
     * PluginDefinition constructor.
     *
     * @param string $id
     * @param string $class
     * @param string $file
     */
    public function __construct($id, $class, $file)
    {
        $this->id = $id;
        $this->class = $class;
        $this->file = $file;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile()
    {
        return $this->class;
    }
}
