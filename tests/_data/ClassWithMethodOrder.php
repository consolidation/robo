<?php

require_once __DIR__ . '/MethodOrderTrait.php';

/**
 * Parent class used for testing the method order of documentation generation.
 */
class MethodOrderParent
{
    public function fromParent()
    {
    }
}

/**
 * A test file. Used for testing documentation generation.
 */
class ClassWithMethodOrder extends MethodOrderParent
{
    use MethodOrderTrait;

    public function zeta()
    {
    }

    public function alpha()
    {
    }
}
