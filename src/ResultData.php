<?php
namespace Robo;

use Robo\Contract\LogResultInterface;
use Consolidation\AnnotatedCommand\ExitCodeInterface;
use Consolidation\AnnotatedCommand\OutputDataInterface;

class ResultData extends \ArrayObject implements ExitCodeInterface, OutputDataInterface
{
    protected $exitCode;
    protected $message;

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

        parent::__construct($data);
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
        return $this->getArrayCopy();
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
        if (!empty($this->message) && !isset($this['already-printed'])) {
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
        $mergedData = $this->getArrayCopy() + $result->getArrayCopy();
        $this->exchangeArray($mergedData);
        return $this;
    }

    public function hasExecutionTime()
    {
        return isset($this['time']);
    }

    public function getExecutionTime()
    {
        if (!$this->hasExecutionTime()) {
            return null;
        }
        return $this['time'];
    }
}
