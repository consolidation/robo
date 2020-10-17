<?php

namespace Robo;

trait LoadAllTasks
{
    use TaskAccessor;

    use Collection\loadTasks;

    // standard tasks

    use Task\Base\loadTasks;
    use Task\Development\loadTasks;
    use Task\Filesystem\loadTasks;
    use Task\File\loadTasks;
    use Task\Archive\loadTasks;
    use Task\Vcs\loadTasks;
    use Task\Logfile\Tasks;

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
    use Task\Logfile\Shortcuts;
}
