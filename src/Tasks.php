<?php
namespace Robo;

use Robo\Common\IO;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;

class Tasks implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use LoadAllTasks;
    use IO;

    /**
     * Return all of the service providers needed by the RoboFile.
     * By default, we return all of the built-in Robo task providers.
     */
    public function getServiceProviders()
    {
        return [];
    }

    protected function stopOnFail($stopOnFail = true)
    {
        Result::$stopOnFail = $stopOnFail;
    }
}
