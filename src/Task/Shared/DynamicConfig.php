<?php
namespace Robo\Task\Shared;

trait DynamicConfig
{
    public function __call($property, $args)
    {
        if (!property_exists($this, $property)) {
            throw new \RuntimeException("Property $property in task ".get_class($this).' does not exists');
        }

        // toggle boolean values
        if (!isset($args[0]) and (is_bool($this->$property))) {
            $this->$property = !$this->$property;
            return $this;
        }

        // append item to array
        if (is_array($this->$property)) {
            if (is_array($args[0])) {
                $this->$property = $args[0];
            } else {
                array_push($this->$property, $args[0]);
            }
            return $this;
        }

        $this->$property = $args[0];
        return $this;
    }
    
    
} 