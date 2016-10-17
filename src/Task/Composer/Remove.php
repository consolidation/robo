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
class Remove extends Base
{
    /**
     * {@inheritdoc}
     */
    protected $action = 'remove';

    /**
     * @return $this
     */
    public function dev()
    {
        $this->option('--dev');
        return $this;
    }

    /**
     * @return $this
     */
    public function noProgress()
    {
        $this->option('--no-progress');
        return $this;
    }

    /**
     * @return $this
     */
    public function noUpdate()
    {
        $this->option('--no-update');
        return $this;
    }

    /**
     * @return $this
     */
    public function updateNoDev()
    {
        $this->option('--update-no-dev');
        return $this;
    }

    /**
     * @return $this
     */
    public function noUpdateWithDependencies()
    {
        $this->option('--no-update-with-dependencies');
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $command = $this->getCommand();
        $this->printTaskInfo('Removing packages: {command}', ['command' => $command]);
        return $this->executeCommand($command);
    }
}
