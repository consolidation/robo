<?php

namespace Robo\Traits;

use Consolidation\AnnotatedCommand\CommandFileDiscovery;
use Robo\Robo;
use Robo\Runner;
use Symfony\Component\Console\Tester\CommandTester;

trait CommandTesterTrait
{
    /** @var string */
    protected $appName;

    /** @var string */
    protected $appVersion;

    /** @var string|array|null */
    protected $commandClasses = null;

    /** @var Runner */
    protected $runner;

    /**
     * Setup the tester.
     *
     * @param string|array|null $commandClasses
     */
    public function setupCommandTester($commandClasses = null)
    {
        // Define our invariants for our test.
        $this->runner = new Runner();
        if (!is_null($commandClasses)) {
            $this->commandClasses = $commandClasses;
        }
    }

    /**
     * @param $command_string
     * @param array $inputs
     * @param array $command_extra
     * @param string|array|null $commandClasses
     * @return array
     */
    protected function executeCommand($command_string, $inputs = [], $command_extra = [], $commandClasses = null)
    {
        $commandClasses = $commandClasses ?? $this->commandClasses;
        $app = $this->runner->getAppForTesting($this->appName, $this->appVersion, $commandClasses);
        $command = $app->get($command_string);
        $tester = new CommandTester($command);
        $tester->setInputs($inputs);
        $status_code = $tester->execute(array_merge(['command' => $command_string], $command_extra));
        Robo::unsetContainer();
        return [trim($tester->getDisplay()), $status_code];
    }
}
