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
        $input = $event->getInput();

        $command = $event->getCommand();

/*
        if ($command->getName() == 'try:formatters') {
            var_export($command->getName());
            print "\n";
            $value = $input->getOption('format');
            print "format is $value\n";
            if ($input->hasOption('format')) {
                print "input has the format option\n";
            }
            $definition = $command->getDefinition();
            $formatOption = $definition->getOption('format');
            $formatOption->setDefault('json');
        }
*/
    }
}
