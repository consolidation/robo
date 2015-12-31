<?php
namespace Robo\TaskCollection;

/**
 * Transient tasks should implement TransientInterface
 * and use Transient.
 */
trait Transient
{
    private $transient = true;

    public function setTransient($transient) {
        $this->transient = $transient;
    }

    public function isTransient() {
        return $this->transient;
    }

    public function complete() {
        if ($this->isTransient()) {
          $this->cleanupTransients();
        }
    }

    public function rollback() {
        $this->cleanupTransients();
    }
}
