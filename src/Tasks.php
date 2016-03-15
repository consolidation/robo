<?php
namespace Robo;

use Robo\Common\IO;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;

class Tasks implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use IO;

    // shortcuts
    use Task\Base\loadShortcuts;
    use Task\FileSystem\loadShortcuts;
    use Task\Vcs\loadShortcuts;

    protected function stopOnFail($stopOnFail = true)
    {
        Result::$stopOnFail = $stopOnFail;
    }

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
     */
    protected function task()
    {
        $args = func_get_args();
        $name = array_shift($args);
        return $this->getContainer()->get("task$name", $args);
    }

    /**
     * Backwards compatibility: convert $this->taskFoo($a, $b) into
     * $this->getContainer()->get('taskFoo', [$a, $b]);
     */
    public function __call($functionName, $args)
    {
        if (preg_match('#^task#', $functionName)) {
            $service = $this->getContainer()->get($functionName, $args);
            if ($service) {
                return $service;
            }
        }
        throw new \Exception("No such method $functionName");
    }
}
