<?php
namespace Robo\TaskCollection;

/**
 * Use CollectionAwareTask in order to be passed a reference to the
 * collection when being run.
 *
 * If a collection-aware-task is being run() directly, without being
 * added to a collection first, then it will be given the global transients
 * collection.  The completion stack for this collection will be executed
 * when the program terminates.
 */
trait CollectionAwareTask
{
    public function run() {
        $this->runInCollection(TransientManager::getCollection());
    }
}
