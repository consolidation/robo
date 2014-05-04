<?php
namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I



class CodeHelper extends \Codeception\Module
{

    public function _before()
    {
    }

    public function _after()
    {
        \AspectMock\Test::clean();
    }
}