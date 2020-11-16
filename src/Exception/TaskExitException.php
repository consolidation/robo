<?php

namespace Robo\Exception;

class TaskExitException extends \Exception
{

    /**
     * @var string
     */
    private $originalMessage;

    /**
     * TaskExitException constructor.
     *
     * @param string|object $class
     * @param string $message
     * @param int $status
     */
    public function __construct($class, $message, $status)
    {
        $this->originalMessage = $message;

        if (is_object($class)) {
            $class = get_class($class);
        }
        parent::__construct("  in task $class \n\n  $message", $status);
    }

    /**
     * @return string
     */
    public function getOriginalMessage() {
        return $this->originalMessage;
    }
}
