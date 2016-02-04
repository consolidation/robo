<?php

namespace Robo\Collection;

/**
 * Temporary tasks should implement TemporaryInterface
 * and use Temporary.
 */
trait Temporary
{
    private $temporary = true;

    public function setTemporary($temporary)
    {
        $this->temporary = $temporary;
    }

    public function isTemporary()
    {
        return $this->temporary;
    }

    public function complete()
    {
        if ($this->isTemporary()) {
            $this->cleanupTemporaries();
        }
    }

    public function rollback()
    {
        $this->cleanupTemporaries();
    }
}
