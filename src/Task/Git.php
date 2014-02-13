<?php
namespace Robo\Task;

use Robo\Output;
use Robo\Result;

/**
 * BundledTasks to do Git stiff
 */
trait Git {

    protected function taskGit($pathToGit = 'git')
    {
        return new GitStack($pathToGit);
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
class GitStack
{
    use Exec;
    use Output;

    protected $git;
    protected $stackCommands = [];

    public function __construct($pathToGit = 'git')
    {
        $this->git = $pathToGit;
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

    public function run()
    {
        $this->printTaskInfo("Running git commands...");
        foreach ($this->stackCommands as $command) {
            $res = $this->taskExec($this->git .' '.$command)->run();
            if (!$res->wasSuccessful()) return $res;
        }
        return Result::success($this);
    }
}