<?php
namespace Robo;

use Robo\Collection\Collection;

trait LoadAllTasks
{
    use TaskAccessor;

    // standard tasks
    use Task\Base\loadTasks;
    use Task\Development\loadTasks;
    use Task\Filesystem\loadTasks;
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
    use Task\Filesystem\loadShortcuts;
    use Task\Vcs\loadShortcuts;

    /**
     * Convenience function. Use:
     *
     * $this->collection();
     *
     * instead of:
     *
     * $this->getContainer()->get('collection');
     *
     * @return Collection
     */
    protected function collection()
    {
        return $this->getContainer()->get('collection');
    }
}
