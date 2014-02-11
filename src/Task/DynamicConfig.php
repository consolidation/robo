<?php
namespace Robo\Task;

trait DynamicConfig
{
    public function __call($property, $args)
    {
        if (!property_exists($this, $property)) {
            throw new \RuntimeException("Property $property in task ".get_class($this).' does not exists');
        }
        if (!isset($args[0]) and (is_bool($this->$property))) {
            $this->$property = !$this->$property;
            return $this;
        }
        $this->$property = $args[0];
        return $this;
    }
    
    
} 