<?php
namespace Robo\Traits;

use Robo\Robo;
use Robo\TaskAccessor;
use Robo\Collection\CollectionBuilder;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

trait TestTasksTrait
{
    use TaskAccessor;

    protected $testPrinter;
    protected $capturedOutput;
    protected $logger;

    public function initTestTasksTrait($commandClass = null, $container = null, $input = null)
    {
        if (!$container) {
            $container = Robo::createDefaultContainer();
        }
        $this->capturedOutput = '';
        $this->testPrinter = new BufferedOutput(OutputInterface::VERBOSITY_DEBUG);

        $app = Robo::createDefaultApplication();
        $config = new \Robo\Config();
        \Robo\Robo::configureContainer($container, $app, $config, $input, $this->testPrinter);

        // Set the application dispatcher
        $app->setDispatcher($container->get('eventDispatcher'));
        $this->logger = $container->get('logger');

        // Use test class as command class if a specific one is not provided
        if (!$commandClass) {
            $commandClass = $this;
        }

        if ($commandClass instanceof Psr\Log\LoggerAwareInterface) {
            $commandClass->setLogger($this->logger);
        }

        // Configure BuilderAwareTrait
        $builder = CollectionBuilder::create($container, $commandClass);
        $commandClass->setBuilder($builder);
        $this->setBuilder($builder);

        return $container;
    }

    public function capturedOutputStream()
    {
        if (!$this->testPrinter) {
            $this->initTestTasksTrait();
        }
        return $this->testPrinter;
    }

    public function logger()
    {
        return $this->logger;
    }

    protected function accumulate()
    {
        $this->capturedOutput .= $this->capturedOutputStream()->fetch();
        return $this->capturedOutput;
    }

    public function assertOutputContains($value)
    {
        $output = $this->accumulate();
        $output = $this->simplify($output);
        $this->assertContains($value, $output);
    }

    public function assertOutputNotContains($value)
    {
        $output = $this->accumulate();
        $output = $this->simplify($output);
        $this->assertNotContains($value, $output);
    }

    public function assertOutputEquals($value)
    {
        $output = $this->accumulate();
        $output = $this->simplify($output);
        $this->assertEquals($value, $output);
    }

    /**
     * Make our output comparisons more platform-agnostic by converting
     * CRLF (Windows) or raw CR (confused output) to a LF (unix/Mac).
     */
    protected function simplify($output)
    {
        $output = str_replace("\r\n", "\n", $output);
        $output = str_replace("\r", "\n", $output);

        return $output;
    }
}
