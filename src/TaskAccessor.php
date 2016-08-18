<?php
namespace Robo;

trait TaskAccessor
{
    /**
     * Commands that use TaskAccessor must provide 'getContainer()'.
     */
    public abstract function getContainer();

    /**
     * Provides the collection builder with access to all of the
     * protected 'task' methods available on this object.
     */
    public function getBuiltClass($fn, $args)
    {
        if (preg_match('#^task[A-Z]#', $fn)) {
            return call_user_func_array([$this, $fn], $args);
        }
    }

    /**
     * Alternative access to instantiate. Use:
     *
     *   $this->task(Foo::class, $a, $b);
     *
     * instead of:
     *
     *   $this->taskFoo($a, $b);
     *
     * The later form is preferred.
     */
    protected function task()
    {
        $args = func_get_args();
        $name = array_shift($args);

        $collectionBuilder = $this->collectionBuilder();
        return $collectionBuilder->build($name, $args);
    }

    /**
     * Get a collection
     *
     * @return \Robo\CollectionCollection
     */
    protected function collection()
    {
        return $this->getContainer()->get('collection');
    }

    /**
     * Get a builder
     *
     * @return \Robo\Collection\CollectionBuilder
     */
    protected function collectionBuilder()
    {
        return $this->getContainer()->get('collectionBuilder', [$this]);
    }
}
