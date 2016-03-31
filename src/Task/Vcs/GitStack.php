<?php
namespace Robo\Task\Vcs;

use Robo\Result;
use Robo\Task\StackBasedTask;
use GitWrapper\GitWorkingCopy;

/**
 * Wrapper for GitWorkingCopy Component.
 * Comands are executed in stack and can be stopped on first fail with `stopOnFail` option.
 *
 * ``` php
 * <?php
 * $git = $this->taskGit()->init('/path/to/new/repo');
 * $currentBranch = $git->getBranch();
 * $git->add('.')
 *     ->commit('Message.');
 *
 * // one line
 * ...
 *
 * ?>
 * ```
 *
 * @method getWrapper()
 * @method getDirectory()
 * @method getOutput()
 * @method clearOutput()
 * @method setCloned($cloned)
 * @method isCloned()
 * @method getStatus()
 * @method hasChanges()
 * @method isTracking()
 * @method isUpToDate()
 * @method isAhead()
 * @method isBehind()
 * @method needsMerge()
 * @method getBranches()
 * @method pushTag($tag, $repository = 'origin', $options = array ())
 * @method pushTags($repository = 'origin', $options = array ())
 * @method fetchAll($options = array ())
 * @method checkoutNewBranch($branch, $options = array ())
 * @method add($filepattern, $options = array ())
 * @method apply()
 * @method bisect($sub_command)
 * @method branch()
 * @method checkout()
 * @method cloneRepository($repository, $options = array ())
 * @method commit()
 * @method config()
 * @method diff()
 * @method fetch()
 * @method grep()
 * @method init($options = array ())
 * @method log()
 * @method merge()
 * @method mv($source, $destination, $options = array ())
 * @method pull()
 * @method push()
 * @method rebase()
 * @method remote()
 * @method reset()
 * @method rm($filepattern, $options = array ())
 * @method show($object, $options = array ())
 * @method status()
 * @method tag()
 * @method clean()
 * @method archive()
 */
class GitStack extends StackBasedTask
{
    protected $state;
    protected $delegate;

    public function __construct($pathToGit = 'git', $state = NULL, $directory = "")
    {
        if (empty($state)) {
            $state = new GitState($pathToGit);
        }
        if (empty($directory)) {
            $directory = getcwd();
        }
        $this->state = $state;
        $this->delegate = new GitWorkingCopy($state->getWrapper(), $directory);
    }

    public function cloneRepo($repo, $to = "", $options = array())
    {
        if (empty($to)) {
            $to = getcwd();
        }
        $this->delegate = new GitWorkingCopy($this->state->getWrapper(), $to);
        $this->cloneRepository($repo, $options);
        return $this;
    }

    public function getState()
    {
        return $this->state;
    }

    protected function getDelegate()
    {
        return $this->delegate;
    }

    public function getWrapper()
    {
        return $this->delegate->getWrapper();
    }

    public function getDirectory()
    {
        return $this->delegate->getDirectory();
    }

    public function getOutput()
    {
        return $this->delegate->getOutput();
    }

    public function setCloned($cloned)
    {
        $this->delegate->setCloned($cloned);
        return $this;
    }

    public function isCloned()
    {
        return $this->delegate->isCloned();
    }

    public function getStatus()
    {
        return $this->delegate->getStatus();
    }

    public function hasChanges()
    {
        return $this->delegate->hasChanges();
    }

    public function isTracking()
    {
        return $this->delegate->isTracking();
    }

    public function isUpToDate()
    {
        return $this->delegate->isUpToDate();
    }

    public function isAhead()
    {
        return $this->delegate->isAhead();
    }

    public function isBehind()
    {
        return $this->delegate->isBehind();
    }

    public function getBranches()
    {
        return $this->delegate->getBranches();
    }

    /**
     * Immediately run an arbitrary git command, and return
     * the result. Useful for querying state.
     */
    public function git($commandLine)
    {
        return $this->getWrapper()->git($commandLine, $this->getDirectory());
    }

    /**
     * Return the current branch
     */
    public function getBranch()
    {
        return $this->git('git rev-parse --abbrev-ref HEAD');
    }

    /**
     * Return the current commit hash
     *
     * @param string $ref
     *   Reference to fetch commit hash for. Could be HEAD~, a branch name, etc.
     */
    public function getCommitHash($ref = 'HEAD')
    {
        return $this->git('git rev-parse ' . $ref);
    }

    protected function _pushTag($tag, $repository = 'origin', $options = array ())
    {
        $this->delegate->pushTag($tag, $repository, $options);
    }

    protected function _pushTags($repository = 'origin', $options = array ())
    {
        $this->delegate->pushTags($repository, $options);
    }

    protected function _fetchAll($options = array ())
    {
        $this->delegate->fetchAll($options);
    }

    protected function _checkoutNewBranch($branch, $options = array ())
    {
        $this->delegate->checkoutNewBranch($branch, $options);
    }

    protected function _add($filepattern, $options = array ())
    {
        $this->delegate->add($filepattern, $options);
    }

    // Slight difference: if options are empty in cpliakas/git-wrapper,
    // then -a is implicitly added. Not so here, though; you need to
    // pass both the message and ['-a'=>true] to get the -a flag.
    // If the first parameter is an array, then both APIs work the same.
    protected function _commit($message, $options = array())
    {
        if (is_string($message)) {
            $options['m'] = $message;
        } else {
            $options = $message;
        }
        $this->delegate->commit($options);
    }

    protected function _init($options = array ())
    {
        $this->delegate->init($options);
    }

    protected function _mv($source, $destination, $options = array ())
    {
        $this->delegate->mv($source, $destination, $options);
    }

    protected function _rm($filepattern, $options = array ())
    {
        $this->delegate->rm($filepattern, $options);
    }

    protected function _show($object, $options = array ())
    {
        $this->delegate->show($object, $options);
    }

    protected function _tag($tag_name, $message = "")
    {
        $this->delegate->tag($tag_name, '-m', $message);
    }
}
