<?php
namespace Codeception\Module;

use Robo\Robo;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

trait SeeInOutputTrait
{
    protected $testPrinter;
    protected $capturedOutput;
    protected $logger;

    public function initSeeInOutputTrait($container, $input = null)
    {
        $this->capturedOutput = '';
        $this->testPrinter = new BufferedOutput(OutputInterface::VERBOSITY_DEBUG);

        $app = Robo::createDefaultApplication();
        $config = new \Robo\Config();
        \Robo\Robo::configureContainer($container, $app, $config, $input, $this->testPrinter);

        // Set the application dispatcher
        $app->setDispatcher($container->get('eventDispatcher'));
        $this->logger = $container->get('logger');
    }

    public function capturedOutputStream()
    {
        return $this->testPrinter;
    }

    public function logger()
    {
        return $this->logger;
    }

    protected function accumulate()
    {
        $this->capturedOutput .= $this->testPrinter->fetch();
        return $this->capturedOutput;
    }

    public function seeInOutput($value)
    {
        $output = $this->accumulate();
        $output = $this->simplify($output);
        $this->assertContains($value, $output);
    }

    public function doNotSeeInOutput($value)
    {
        $output = $this->accumulate();
        $output = $this->simplify($output);
        $this->assertNotContains($value, $output);
    }

    public function seeOutputEquals($value)
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
