<?php
namespace Codeception\Module;

use Robo\Robo;
use Symfony\Component\Console\Output\ConsoleOutput;
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
        $this->logger = new \Robo\Log\RoboLogger($this->testPrinter);
        $progressBar = new \Symfony\Component\Console\Helper\ProgressBar($this->testPrinter);

        $config = new \Robo\Config();
        \Robo\Robo::configureContainer($container, $config, $input, $this->testPrinter);
        $container->add('output', $this->testPrinter);
        $container->add('progressBar', $progressBar);
        $container->add('progressIndicator', new \Robo\Common\ProgressIndicator($progressBar, $this->testPrinter));
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
        $this->assertContains($value, $output);
    }

    public function doNotSeeInOutput($value)
    {
        $output = $this->accumulate();
        $this->assertNotContains($value, $output);
    }

    public function seeOutputEquals($value)
    {
        $output = $this->accumulate();
        $this->assertEquals($value, $output);
    }
}
