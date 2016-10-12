<?php

namespace Robo\Common;

use Robo\Robo;
use Robo\Collection\CollectionBuilder;

trait BuilderAwareTrait
{
    /**
     * @var \Robo\Collection\CollectionBuilder
     */
    protected $builder;

    /**
     * @inheritdoc
     */
    public function setBuilder(CollectionBuilder $builder)
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @inheritdoc
     */
    protected function collectionBuilder()
    {
        return $this->getBuilder()->newBuilder();
    }
}
