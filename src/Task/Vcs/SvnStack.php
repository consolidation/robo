<?php
namespace Robo\Task\Vcs;

use Robo\Contract\CommandInterface;
use Robo\Contract\TaskInterface;
use Robo\Output;
use Robo\Result;

/**
 * Runs Svn commands in stack. You can use `stopOnFail()` to point that stack should be terminated on first fail.
 *
 * ``` php
 * <?php
 * taskSvn::stack()
 *  ->checkout('http://svn.collab.net/repos/svn/trunk')
 *  ->run()
 *
 * // alternatively
 * taskSvn::_checkout('http://svn.collab.net/repos/svn/trunk');
 *
 * taskSvn::init('username', 'password')
 *  ->stopOnFail()
 *  ->update()
 *  ->add('doc/*')
 *  ->commit('doc updated')
 *  ->run();
 * ?>
 * ```
 */
class SvnStack implements TaskInterface, CommandInterface
{
    use Output;
    use \Robo\Common\Stackable;
    use \Robo\Common\Executable;

    protected $svn;
    protected $stackCommands = [];
    protected $stopOnFail = false;
    protected $result;

    public function __construct($username='', $password='', $pathToSvn = 'svn')
    {

        $this->svn = $pathToSvn;
        if (!empty($username)) {
            $this->svn .= " --username $username";
        }
        if (!empty($password)) {
            $this->svn .= " --password $password";
        }
        $this->result = Result::success($this);
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
            $this->result = $this->executeCommand($this->svn .' '.$command);
            if (!$this->result->wasSuccessful() and $this->stopOnFail) {
                return $this->result;
            }
        }
        return Result::success($this);
    }
}
