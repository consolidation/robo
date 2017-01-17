<?php
namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Robo\Robo;
use Symfony\Component\Console\Output\ConsoleOutput;

class CodeHelper extends \Codeception\Module
{
    use SeeInOutputTrait;

    protected static $container;

    public function _before(\Codeception\TestCase $test)
    {
        static::$container = new \League\Container\Container();
        Robo::setContainer(static::$container);
        $this->initSeeInOutputTrait(static::$container);
    }

    public function _after(\Codeception\TestCase $test)
    {
        // Ensure that $stopOnFail global static is reset, as tests
        // that set it to true will force an exception, and therefor
        // will not have a chance to clean this up.
        \Robo\Result::$stopOnFail = false;

        \AspectMock\Test::clean();
        $consoleOutput = new ConsoleOutput();
        static::$container->add('output', $consoleOutput);
        static::$container->add('logger', new \Consolidation\Log\Logger($consoleOutput));
    }
}
