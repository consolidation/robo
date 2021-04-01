<?php

namespace Robo\Task\Composer;

/**
 * Composer Show
 *
 * ``` php
 * <?php
 *  // simple execution
 *  $this->taskComposerRequire()->dependency('foo/bar')->run();
 *
 *  // inspect output
 *  $this->taskComposerRequire()->dependency('foo/bar')->format('json')->printOutput(false)->run();
 *  $dependencyInfo = $result->getOutputData();
 * ?>
 * ```
 */
class Show extends Base
{
    /**
     * {@inheritdoc}
     */
    protected $action = 'show';

    /**
     * composer dependency
     *
     * @param string $project
     *
     * @return $this
     */
    public function dependency($project)
    {
        $project = (array)$project;

        $this->args($project);
        return $this;
    }

    /**
     * adds `format` option to composer
     *
     * @param string $format
     *
     * @return $this
     */
    public function format($format = 'text')
    {
        $this->option('--format', $format);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $command = $this->getCommand();
        $this->printTaskInfo('Showing packages: {command}', ['command' => $command]);
        return $this->executeCommand($command);
    }
}
