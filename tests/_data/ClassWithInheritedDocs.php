<?php

/**
 * Oldest ancestor. Used for testing documentation generation.
 */
class InheritedDocsGrandparent
{
    /**
     * @var string Grandparent property description
     */
    public $value;

    /**
     * Grandparent method description
     */
    public function describe()
    {
    }
}

/**
 * Nearest parent. Used for testing documentation generation.
 */
class InheritedDocsParent extends InheritedDocsGrandparent
{
    /**
     * @var string Parent property description
     */
    public $value;

    /**
     * Parent method description
     */
    public function describe()
    {
    }
}

/**
 * A test file. Used for testing documentation generation.
 */
class ClassWithInheritedDocs extends InheritedDocsParent
{
    public $value;

    public function describe()
    {
    }
}
