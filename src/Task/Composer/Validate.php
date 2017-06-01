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
     * @return $this
     */
    public function noCheckAll()
    {
        $this->option('--no-check-all');
        return $this;
    }

    /**
     * @return $this
     */
    public function noCheckLock()
    {
        $this->option('--no-check-lock');
        return $this;
    }

    /**
     * @return $this
     */
    public function noCheckPublish()
    {
        $this->option('--no-check-publish');
        return $this;
    }

    /**
     * @return $this
     */
    public function withDependencies()
    {
        $this->option('--with-dependencies');
        return $this;
    }

    /**
     * @return $this
     */
    public function strict()
    {
        $this->option('--strict');
        return $this;
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
