<?php
namespace Robo\Task;

use Robo\Output;
use Robo\Result;

/**
 * BundledTasks to do Git stuff
 */
trait Git {

    protected function taskGit($pathToGit = 'git')
    {
        return new GitStackTask($pathToGit);
    }

}

/**
 * Runs Git commands in stack
 *
 * ``` php
 * <?php
 * $this->taskGit()
 *  ->add('-A')
 *  ->commit('adding everything')
 *  ->push('origin','master')
 *  ->run()
 *
 * $this->taskGit()
 *  ->add('doc/*')
 *  ->commit('doc updated')
 *  ->push()
 *  ->run();
 * ?>
 * ```
 */
class GitStackTask implements TaskInterface
{
    use Exec;
    use Output;

    protected $git;
    protected $stackCommands = [];
    protected $result;

    public function __construct($pathToGit = 'git')
    {
        $this->git = $pathToGit;
        $this->result = Result::success($this);
    }

    public function cloneRepo($repo, $to = "")
    {
        $this->stackCommands[]= "clone $repo $to";
        return $this;
    }

    public function add($pattern)
    {
        $this->stackCommands[]= "add $pattern";
        return $this;
    }

    public function commit($message, $options = "")
    {
        $this->stackCommands[] = "commit -m '$message' $options";
        return $this;
    }

    public function pull($origin = '', $branch = '')
    {
        $this->stackCommands[] = "pull $origin $branch";
        return $this;        
    }

    public function push($origin = '', $branch = '')
    {
        $this->stackCommands[] = "push $origin $branch";
        return $this;
    }

    public function checkout($branch)
    {
        $this->stackCommands[] = "checkout $branch";
        return $this;
    }

    public function run()
    {
        $this->printTaskInfo("Running git commands...");
        foreach ($this->stackCommands as $command) {
            $this->result = $this->taskExec($this->git .' '.$command)->run();
            if (!$this->result->wasSuccessful()) return $this->result;
        }
        return Result::success($this);
    }
}