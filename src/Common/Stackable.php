<?php
namespace Robo\Common;

trait Stackable
{
    public static function stack()
    {
        return new static;
    }

    public static function init()
    {
        return new static;
    }

    public function stopOnFail($stop = true)
    {
        $this->stopOnFail = $stop;
        return $this;
    }

} 