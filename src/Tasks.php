<?php
namespace Robo;

class Tasks
{
    // standard tasks
    use Task\Base\loadTasks;
    use Task\Development\loadTasks;
    use Task\FileSystem\loadTasks;
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

    // shortcuts
    use Task\Base\loadShortcuts;
    use Task\FileSystem\loadShortcuts;
    use Task\Vcs\loadShortcuts;

    use Output;

    protected function stopOnFail($stopOnFail = true)
    {
        Result::$stopOnFail = $stopOnFail;
    }
}
