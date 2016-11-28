<?php
namespace Robo;

use \CliGuy;

class SimulatedCest
{
    public function _before(CliGuy $I)
    {
        $I->amInPath(codecept_data_dir().'sandbox');
    }

    public function toSimulateDirCreation(CliGuy $I)
    {
        // Set up a collection to add tasks to
        $collection = $I->collectionBuilder();
        $collection->simulated(true);

        // Set up a filesystem stack
        $collection->taskFilesystemStack()
            ->mkdir('simulatedir')
            ->touch('simulatedir/error.txt');

        // Run the task collection; now the files should be present
        $collection->run();
        // Nothing should be created in simulated mode
        $I->dontSeeFileFound('simulatedir/error.txt');
        $I->seeInOutput('[Simulator] Simulating Filesystem\FilesystemStack()');
    }
}
