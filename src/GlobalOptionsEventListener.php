<?php
namespace Robo;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Robo\Contract\ConfigAwareInterface;
use Robo\Common\ConfigAwareTrait;

class GlobalOptionsEventListener implements EventSubscriberInterface, ConfigAwareInterface
{
    use ConfigAwareTrait;

    /**
     * @{@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [ConsoleEvents::COMMAND => 'setGlobalOptions'];
    }

    /**
     * Before a Console command runs, examine the global
     * commandline options from the event Input, and set
     * configuration values as appropriate.
     *
     * @param ConsoleCommandEvent $event
     */
    public function setGlobalOptions(ConsoleCommandEvent $event)
    {
        $this->getConfig()->setGlobalOptions($event->getInput());
    }
}
