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
     * {@inheritdoc}
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
     * @param \Symfony\Component\Console\Event\ConsoleCommandEvent $event
     */
    public function setGlobalOptions(ConsoleCommandEvent $event)
    {
        $config = $this->getConfig();
        $input = $event->getInput();
        $globalOptions = $config->getGlobalOptionDefaultValues();

        foreach ($globalOptions as $option => $default) {
            $value = $input->hasOption($option) ? $input->getOption($option) : null;
            // Unfortunately, the `?:` operator does not differentate between `0` and `null`
            if (!isset($value)) {
                $value = $default;
            }
            $config->set($option, $value);
        }
    }
}
