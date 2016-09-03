<?php
namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Robo\Robo;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

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
        \AspectMock\Test::clean();
        $consoleOutput = new ConsoleOutput();
        static::$container->add('output', $consoleOutput);
        static::$container->add('logger', new \Consolidation\Log\Logger($consoleOutput));
    }
}
