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
    /**
     * {@inheritdoc}
     */
    protected $action = 'validate';

    /**
     * @var string
     */
    protected $noCheckAll;

    /**
     * @var string
     */
    protected $noCheckLock;

    /**
     * @var string
     */
    protected $noCheckPublish;

    /**
     * @var string
     */
    protected $withDependencies;

    /**
     * @var string
     */
    protected $strict;

    /**
     * @return $this
     */
    public function noCheckAll()
    {
        $this->noCheckAll = '--no-check-all';
        return $this;
    }

    /**
     * @return $this
     */
    public function noCheckLock()
    {
        $this->noCheckLock = '--no-check-lock';
        return $this;
    }

    /**
     * @return $this
     */
    public function noCheckPublish()
    {
        $this->noCheckPublish = '--no-check-publish';
        return $this;
    }

    /**
     * @return $this
     */
    public function withDependencies()
    {
        $this->withDependencies = '--with-dependencies';
        return $this;
    }

    /**
     * @return $this
     */
    public function strict()
    {
        $this->strict = '--strict';
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand()
    {
        $this->option($this->noCheckAll);
        $this->option($this->noCheckLock);
        $this->option($this->noCheckPublish);
        $this->option($this->withDependencies);
        $this->option($this->strict);

        return parent::getCommand();
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $command = $this->getCommand();
        $this->printTaskInfo('Validating composer.json: {command}', ['command' => $command]);
        return $this->executeCommand($command);
    }
}
