<?php
namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Robo\Runner;
use Symfony\Component\Console\Output\NullOutput;

class CodeHelper extends \Codeception\Module
{

    public function _before(\Codeception\TestCase $test)
    {
        Runner::setPrinter(new NullOutput());
    }

    public function _after(\Codeception\TestCase $test)
    {
        \AspectMock\Test::clean();
        Runner::setPrinter(null);

    }
}