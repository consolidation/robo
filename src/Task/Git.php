<?php
namespace Robo\Task;

use Robo\Output;
use Robo\Result;
use Robo\Task\Shared\CommandInterface;
use Robo\Task\Shared\TaskInterface;

trait Git {

    protected function taskGitStack($pathToGit = 'git')
    {
        return new GitStackTask($pathToGit);
    }

}

/**
 * Runs Git commands in stack. You can use `stopOnFail()` to point that stack should be terminated on first fail.
 *
 * ``` php
 * <?php
 * $this->taskGitStack()
 *  ->stopOnFail()
 *  ->add('-A')
 *  ->commit('adding everything')
 *  ->push('origin','master')
 *  ->run()
 *
 * $this->taskGitStack()
 *  ->stopOnFail()
 *  ->add('doc/*')
 *  ->commit('doc updated')
 *  ->push()
 *  ->run();
 * ?>
 * ```
 */
class GitStackTask implements TaskInterface, CommandInterface
{
    use Exec;
    use Output;

    protected $git;
    protected $stackCommands = [];
    protected $stopOnFail = false;
    protected $result;

    public function __construct($pathToGit = 'git')
    {
        $this->git = $pathToGit;
        $this->result = Result::success($this);
    }

    /**
     * Executes `git clone`
     *
     * @param $repo
     * @param string $to
     * @return $this
     */
    public function cloneRepo($repo, $to = "")
    {
        $this->stackCommands[]= "clone $repo $to";
        return $this;
    }

    /**
     * Git commands in stack will stop if any of commands were unsuccessful
     *
     * @return $this
     */
    public function stopOnFail()
    {
        $this->stopOnFail = true;
        return $this;
    }

    /**
     * Executes `git add` command with files to add pattern
     *
     * @param $pattern
     * @return $this
     */
    public function add($pattern)
    {
        $this->stackCommands[]= "add $pattern";
        return $this;
    }

    /**
     * Executes `git commit` command with a message
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
     * Executes `git pull` command.
     *
     * @param string $origin
     * @param string $branch
     * @return $this
     */
    public function pull($origin = '', $branch = '')
    {
        $this->stackCommands[] = "pull $origin $branch";
        return $this;        
    }

    /**
     * Executes `git push` command
     *
     * @param string $origin
     * @param string $branch
     * @return $this
     */
    public function push($origin = '', $branch = '')
    {
        $this->stackCommands[] = "push $origin $branch";
        return $this;
    }

    /**
     * Executes `git checkout` command
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
        $commands = array_map(function($c) { return $this->git .' '. $c; }, $this->stackCommands);
        return implode(' && ', $commands);
    }

    public function run()
    {
        $this->printTaskInfo("Running git commands...");
        foreach ($this->stackCommands as $command) {
            $this->result = $this->taskExec($this->git .' '.$command)->run();
            if (!$this->result->wasSuccessful() and $this->stopOnFail) {
                return $this->result;
            }
        }
        return Result::success($this);
    }
}