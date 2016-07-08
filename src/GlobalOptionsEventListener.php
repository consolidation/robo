<?php
namespace Robo;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GlobalOptionsEventListener implements EventSubscriberInterface
{
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
        /* @var Input $input */
        $input = $event->getInput();

        // Need a better way to handle global options.
        // This is slightly improved from before.
        Config::setGlobalOptions($input);
        TaskBuilder::setDefaultSimulated(Config::isSimulated());
    }
}
