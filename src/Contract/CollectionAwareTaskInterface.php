<?php
namespace Robo\Contract;

use Robo\TaskCollection\Collection;

/**
 * A Robo task that creates transients, or desires access
 * to the Collection object for any other reason should
 * implement CollectionAwareTaskInterface, and use the
 * CollectionAwareTask trait.
 *
 * If you do this, implement your action in runInCollection()
 * rather than in run(); you will then have access to the
 * collection to, for example, use $collection->registerCompletion()
 * to register a completion task, e.g. to delete your transients.
 *
 * If some client calls a container-aware task via run() rather
 * than collect(), then the completion tasks will not run until
 * the program terminates.
 *
 * Interface CollectionAwareTaskInterface
 * @package Robo\Contract
 */
interface CollectionAwareTaskInterface extends TaskInterface
{
    /**
     * @return \Robo\Result
     */
    function runInCollection(Collection $collection);
}
