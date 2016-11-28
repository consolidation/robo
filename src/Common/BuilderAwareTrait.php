<?php

namespace Robo\Common;

use Robo\Collection\CollectionBuilder;

trait BuilderAwareTrait
{
    /**
     * @var \Robo\Collection\CollectionBuilder
     */
    protected $builder;

    /**
     * @see \Robo\Contract\BuilderAwareInterface::setBuilder()
     *
     * @param \Robo\Collection\CollectionBuilder $builder
     *
     * @return $this
     */
    public function setBuilder(CollectionBuilder $builder)
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * @see \Robo\Contract\BuilderAwareInterface::getBuilder()
     *
     * @return \Robo\Collection\CollectionBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @return \Robo\Collection\CollectionBuilder
     */
    protected function collectionBuilder()
    {
        return $this->getBuilder()->newBuilder();
    }
}
