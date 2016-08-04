<?php
namespace Robo;

trait LoadAllTasks
{
    use TaskAccessor;

    /**
     * Return all of the service providers needed by the RoboFile.
     * By default, we return all of the built-in Robo task providers.
     */
    public function getServiceProviders()
    {
        return
        [
            \Robo\Collection\loadTasks::getCollectionServices(),
            \Robo\Task\ApiGen\loadTasks::getApiGenServices(),
            \Robo\Task\Archive\loadTasks::getArchiveServices(),
            \Robo\Task\Assets\loadTasks::getAssetsServices(),
            \Robo\Task\Base\loadTasks::getBaseServices(),
            \Robo\Task\Npm\loadTasks::getNpmServices(),
            \Robo\Task\Bower\loadTasks::getBowerServices(),
            \Robo\Task\Gulp\loadTasks::getGulpServices(),
            \Robo\Task\Composer\loadTasks::getComposerServices(),
            \Robo\Task\Development\loadTasks::getDevelopmentServices(),
            \Robo\Task\Docker\loadTasks::getDockerServices(),
            \Robo\Task\File\loadTasks::getFileServices(),
            \Robo\Task\Filesystem\loadTasks::getFilesystemServices(),
            \Robo\Task\Remote\loadTasks::getRemoteServices(),
            \Robo\Task\Testing\loadTasks::getTestingServices(),
            \Robo\Task\Vcs\loadTasks::getVcsServices(),
        ];
    }

    use Collection\loadTasks;

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
}
