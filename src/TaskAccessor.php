<?php
namespace Robo;

use Robo\Common\BuilderAwareTrait;

trait TaskAccessor
{
    use BuilderAwareTrait;

    /**
     * Provides the collection builder with access to all of the
     * protected 'task' methods available on this object.
     */
    public function getBuiltTask($fn, $args)
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
}
