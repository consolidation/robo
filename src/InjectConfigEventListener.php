<?php
namespace Robo;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Robo\Contract\ConfigAwareInterface;
use Robo\Common\ConfigAwareTrait;

class InjectConfigEventListener implements EventSubscriberInterface, ConfigAwareInterface
{
    use ConfigAwareTrait;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [ConsoleEvents::COMMAND => 'injectConfiguration'];
    }

    /**
     * Before a Console command runs, inject configuration settings
     * for this command into the default value of the options of
     * this command.
     *
     * @param \Symfony\Component\Console\Event\ConsoleCommandEvent $event
     */
    public function injectConfiguration(ConsoleCommandEvent $event)
    {
        $config = $this->getConfig();

        $command = $event->getCommand();
        $commandName = $command->getName();
        $commandName = str_replace(':', '.', $commandName);
        $definition = $command->getDefinition();
        $options = $definition->getOptions();
        foreach ($options as $option => $inputOption) {
            $key = str_replace('.', '-', $option);
            $configKey = "command.{$commandName}.options.{$key}";
            if ($config->has($configKey)) {
                $inputOption->setDefault($config->get($configKey));
            }
        }
    }
}
