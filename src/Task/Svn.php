<?php
namespace Robo\Task;

use Robo\Output;
use Robo\Result;
use Robo\Task\Shared\CommandInterface;
use Robo\Task\Shared\TaskInterface;

trait Svn {

    protected function taskSvnStack($pathToSvn = 'svn', $username = '', $password = '')
    {
        return new SvnStackTask($pathToSvn, $username, $password);
    }

}

/**
 * Runs Svn commands in stack. You can use `stopOnFail()` to point that stack should be terminated on first fail.
 *
 * ``` php
 * <?php
 * $this->taskSvnStack()
 *  ->stopOnFail()
 *  ->add()
 *  ->commit('adding everything')
 *  ->run()
 *
 * $this->taskSvnStack()
 *  ->stopOnFail()
 *  ->update()
 *  ->add('doc/*')
 *  ->commit('doc updated')
 *  ->run();
 * ?>
 * ```
 */
class SvnStackTask implements TaskInterface, CommandInterface
{
    use Exec;
    use Output;

    protected $svn;
    protected $stackCommands = [];
    protected $stopOnFail = false;
    protected $result;

    public function __construct($pathToSvn='svn', $username='', $password='')
    {
        $this->svn = $pathToSvn;
        if (! empty($username)) {
            $this->svn .= " --username $username";
        }
        if (! empty($password)) {
            $this->svn .= " --password $password";
        }
        $this->result = Result::success($this);
    }

    /**
     * Svn commands in stack will stop if any of commands were unsuccessful
     *
     * @return $this
     */
    public function stopOnFail()
    {
        $this->stopOnFail = true;
        return $this;
    }

    /**
     * Updates `svn update` command
     *
     * @return $this;
     */
    public function update($path='')
    {
        $this->stackCommands[] = "update $path";
        return $this;
    }

    /**
     * Executes `svn add` command with files to add pattern
     *
     * @param $pattern
     * @return $this
     */
    public function add($pattern='')
    {
        $this->stackCommands[]= "add $pattern";
        return $this;
    }

    /**
     * Executes `svn commit` command with a message
     *
     * @param $message
     * @param string $options
     * @return $this
     */
    public function commit($message, $options = "")
    {
        $this->stackCommands[] = "commit -m '$message' $options";
        return $this;
    }

    /**
     * Executes `svn checkout` command
     *
     * @param $branch
     * @return $this
     */
    public function checkout($branch)
    {
        $this->stackCommands[] = "checkout $branch";
        return $this;
    }

    public function getCommand()
    {
        $commands = array_map(function($c) { return $this->svn .' '. $c; }, $this->stackCommands);
        return implode(' && ', $commands);
    }

    public function run()
    {
        $this->printTaskInfo("Running svn commands...");
        foreach ($this->stackCommands as $command) {
            $this->result = $this->taskExec($this->svn .' '.$command)->run();
            if (!$this->result->wasSuccessful() and $this->stopOnFail) {
                return $this->result;
            }
        }
        return Result::success($this);
    }
}
