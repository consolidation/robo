<?php
namespace Robo\Task\Composer;

/**
 * Composer Require
 *
 * ``` php
 * <?php
 * // simple execution
 * $this->taskComposerRequire()->dependency('foo/bar', '^.2.4.8')->run();
 * ?>
 * ```
 */
class RequireDependency extends Base
{
    /**
     * {@inheritdoc}
     */
    protected $action = 'require';

    /**
     * 'require' is a keyword, so it cannot be a method name.
     * @return $this
     */
    public function dependency($project, $version = null)
    {
        if (isset($version)) {
            $project .= ":$version";
        }
        $this->arg($project);
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
