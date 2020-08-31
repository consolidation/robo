<?php

namespace Robo\Common;

use Robo\Contract\InflectionInterface;

trait InflectionTrait
{
    /**
     * Ask the provided parent class to inject all of the dependencies
     * that it has and we need.
     *
     * @param \Robo\Contract\InflectionInterface $parent
     *
     * @return $this
     */
    public function inflect(InflectionInterface $parent)
    {
        $parent->injectDependencies($this);
        return $this;
    }

    /**
     * Inflect the provided parent object if it implements InflectionInterface
     *
     * @param mixed $parent
     * @return $this
     */
    public function inflectIf($parent)
    {
        if (isset($parent) && ($parent instanceof InflectionInterface)) {
            return $this->inflect($parent);
        }
        return $this;
    }
}
