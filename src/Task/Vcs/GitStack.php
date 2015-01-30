<?php
namespace Robo\Task\Vcs;

use Robo\Task\CommandStack;

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
class GitStack extends CommandStack
{

    public function __construct($pathToGit = 'git')
    {
        $this->executable = $pathToGit;
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
        return $this->exec(['clone', $repo, $to]);
    }

    /**
     * Executes `git add` command with files to add pattern
     *
     * @param $pattern
     * @return $this
     */
    public function add($pattern)
    {
        return $this->exec([__FUNCTION__, $pattern]);
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
        return $this->exec([__FUNCTION__, "-m '$message'", $options]);
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
        return $this->exec([__FUNCTION__, $origin, $branch]);
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
        return $this->exec([__FUNCTION__, $origin, $branch]);
    }

    /**
     * Performs git merge
     *
     * @param string $branch
     * @return $this
     */
    public function merge($branch)
    {
        return $this->exec([__FUNCTION__, $branch]);
    }

    /**
     * Executes `git checkout` command
     *
     * @param $branch
     * @return $this
     */
    public function checkout($branch)
    {
        return $this->exec([__FUNCTION__, $branch]);
    }

    public function run()
    {
        $this->printTaskInfo("Running git commands...");
        return parent::run();
    }
}
