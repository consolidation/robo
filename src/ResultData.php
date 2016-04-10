<?php
namespace Robo;

use Robo\Contract\LogResultInterface;
use Consolidation\AnnotationCommand\ExitCodeInterface;
use Consolidation\AnnotationCommand\OutputDataInterface;

class ResultData implements \ArrayAccess, \IteratorAggregate, ExitCodeInterface, OutputDataInterface
{
    protected $exitCode;
    protected $message;
    protected $data = [];

    const EXITCODE_OK = 0;
    const EXITCODE_ERROR = 1;
    /** Symfony Console handles these conditions; Robo returns the status
    code selected by Symfony. These are here for documentation purposes. */
    const EXITCODE_MISSING_OPTIONS = 2;
    const EXITCODE_COMMAND_NOT_FOUND = 127;

    /** The command was aborted because the user chose to cancel it at some prompt.
    This exit code is arbitrarily the same as EX_TEMPFAIL in sysexits.h, although
    note that shell error codes are distinct from C exit codes, so this alignment
    not particularly meaningful. */
    const EXITCODE_USER_CANCEL = 75;

    public function __construct($exitCode = self::EXITCODE_OK, $message = '', $data = [])
    {
        $this->exitCode = $exitCode;
        $this->message = $message;
        $this->data = $data;
    }

    public static function message($message, $data = [])
    {
        return new self(self::EXITCODE_OK, $message, $data);
    }

    public static function cancelled($message = '', $data = [])
    {
        return new ResultData(self::EXITCODE_USER_CANCEL, $message, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    public function getOutputData()
    {
        if (!empty($this->message) && !array_key_exists('already-printed', $this->data)) {
            return $this->message;
        }
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    public function getExecutionTime()
    {
        if (!is_array($this->data)) {
            return null;
        }
        if (!isset($this->data['time'])) {
            return null;
        }
        $rawTime = $this->data['time'];
        return round($rawTime, 3).'s';
    }

    public function wasSuccessful()
    {
        return $this->exitCode === self::EXITCODE_OK;
    }

    public function wasCancelled()
    {
        return $this->exitCode == EXITCODE_USER_CANCEL;
    }

    /**
     * Merge another result into this result.  Data already
     * existing in this result takes precedence over the
     * data in the Result being merged.
     */
    public function merge(ResultData $result)
    {
        $this->data += $result->getData();
        return $this;
    }

    /**
     * \ArrayAccess accessor for `isset()`
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * \ArrayAccess accessor for array data access.
     */
    public function offsetGet($offset)
    {
        if (isset($this->data[$offset])) {
            return $this->data[$offset];
        }
    }

    /**
     * \ArrayAccess method for array assignment.
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * \ArrayAccess method for `unset`
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * \IteratorAggregate accessor for `foreach`.
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }
}
