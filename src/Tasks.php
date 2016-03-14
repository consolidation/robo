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

    protected function collection()
    {
        return $this->getContainer()->get('collection');
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
