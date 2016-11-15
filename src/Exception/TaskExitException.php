<?php
namespace Robo\Exception;

class TaskExitException extends \Exception
{
    public function __construct($class, $message, $status)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        parent::__construct("  in task $class \n\n  $message", $status);
    }
}
