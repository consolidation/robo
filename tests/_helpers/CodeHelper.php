<?php
namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I



class CodeHelper extends \Codeception\Module
{

    public function _before(\Codeception\TestCase $test)
    {
    }

    public function _after(\Codeception\TestCase $test)
    {
        \AspectMock\Test::clean();
    }
}