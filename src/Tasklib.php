<?php
namespace Robo;

trait Tasklib
{
    // standard tasks
    use Task\Base\loadTasks;
    use Task\Development\loadTasks;
    use Task\FileSystem\loadTasks;
    use Task\File\loadTasks;
    use Task\Archive\loadTasks;
    use Task\Vcs\loadTasks;

    // package managers
    use Task\Composer\loadTasks;
    use Task\Bower\loadTasks;
    use Task\Npm\loadTasks;

    // assets
    use Task\Assets\loadTasks;

    // 3rd-party tools
    use Task\Remote\loadTasks;
    use Task\Testing\loadTasks;
    use Task\ApiGen\loadTasks;
    use Task\Docker\loadTasks;

    // task runners
    use Task\Gulp\loadTasks;

    // shortcuts
    use Task\Base\loadShortcuts;
    use Task\FileSystem\loadShortcuts;
    use Task\Vcs\loadShortcuts;

    /**
     * Commands that use TaskLib must provide 'getContainer()'.
     */
    abstract function getContainer();

    /**
     * Convenience function. Use:
     *
     * $this->collection();
     *
     * instead of:
     *
     * $this->getContainer()->get('collection');
     */
    protected function collection()
    {
        return $this->getContainer()->get('collection');
    }

    /**
     * Convenience function. Use:
     *
     * $this->task('Foo', $a, $b);
     *
     * instead of:
     *
     * $this->getContainer()->get('taskFoo', [$a, $b]);
     *
     * Note that most tasks will define another convenience
     * function, $this->taskFoo($a, $b), declared in a
     * 'loadTasks' trait in the task's namespace.  These
     * 'loadTasks' convenience functions typically will call
     * $this->task() to ensure that task objects are fetched
     * from the container, so that their dependencies may be
     * automatically resolved.
     */
    protected function task()
    {
        $args = func_get_args();
        $name = array_shift($args);
        // We'll allow callers to include the literal 'task'
        // or not, as they wish; however, the container object
        // that we fetch must always begin with 'task'
        if (!preg_match('#^task#', $name)) {
            $name = "task$name";
        }
        return $this->getContainer()->get($name, $args);
    }
}
