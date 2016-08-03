<?php
namespace Robo\Task\Composer;

/**
 * Composer Validate
 *
 * ``` php
 * <?php
 * // simple execution
 * $this->taskComposerValidate()->run();
 * ?>
 * ```
 */
class Validate extends Base
{
    protected $action = 'validate';

    protected $noCheckAll;
    protected $noCheckLock;
    protected $noCheckPublish;
    protected $withDependencies;
    protected $strict;

    public function noCheckAll()
    {
        $this->noCheckAll = '--no-check-all';
        return $this;
    }

    public function noCheckLock()
    {
        $this->noCheckLock = '--no-check-lock';
        return $this;
    }

    public function noCheckPublish()
    {
        $this->noCheckPublish = '--no-check-publish';
        return $this;
    }

    public function withDependencies()
    {
        $this->withDependencies = '--with-dependencies';
        return $this;
    }

    public function strict()
    {
        $this->strict = '--strict';
        return $this;
    }

    public function getCommand()
    {
        $this->option($this->noCheckAll);
        $this->option($this->noCheckLock);
        $this->option($this->noCheckPublish);
        $this->option($this->withDependencies);
        $this->option($this->strict);

        return parent::getCommand();
    }

    public function run()
    {
        $command = $this->getCommand();
        $this->printTaskInfo('Validating composer.json: {command}', ['command' => $command]);
        return $this->executeCommand($command);
    }
}
