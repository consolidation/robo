<?php
namespace Robo\Task\Composer;

/**
 * Composer CreateProject
 *
 * ``` php
 * <?php
 * // simple execution
 * $this->taskComposerCreateProject()->source('foo/bar')->target('myBar')->run();
 * ?>
 * ```
 */
class CreateProject extends Base
{
    /**
     * {@inheritdoc}
     */
    protected $action = 'create-project';

    protected $source;
    protected $target = '';
    protected $version = '';

    /**
     * @return $this
     */
    public function source($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return $this
     */
    public function target($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * @return $this
     */
    public function version($version)
    {
        $this->version = $version;
        return $this;
    }

    public function keepVcs($keep = true)
    {
        if ($keep) {
            $this->option('--keep-vcs');
        }
        return $this;
    }

    public function install($install = true)
    {
        if (!$install) {
            return $this->noInstall();
        }
        return $this;
    }

    public function noInstall()
    {
        $this->option('--no-install');
        return $this;
    }

    /**
     * @return $this
     */
    public function repository($repository)
    {
        $this->option('repository', $repository);
        return $this;
    }

    public function buildCommand()
    {
        $this->arg($this->source);
        $this->arg($this->target);
        $this->arg($this->version);

        return parent::buildCommand();
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $command = $this->getCommand();
        $this->printTaskInfo('Creating project: {command}', ['command' => $command]);
        return $this->executeCommand($command);
    }
}
