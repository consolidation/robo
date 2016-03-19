<?php
namespace Robo;

use phpDocumentor\Reflection\DocBlock\Tag\ParamTag;
use phpDocumentor\Reflection\DocBlock;

class TaskInfo
{
    const PARAM_IS_REQUIRED = '__param_is_required__';

    /**
     * @var \ReflectionMethod
     */
    protected $reflection;

    /**
     * @var boolean
     */
    protected $docBlockIsParsed;

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string
     */
    protected $help = '';

    /**
     * @var array
     */
    protected $tagProcessors = [
        'param' => 'processArgumentTag',
        'option' => 'processOptionTag',
        'aliases' => 'processAliases',
        'usage' => 'processUsageTag',
        'description' => 'processAlternateDescriptionTag',
        'desc' => 'processAlternateDescriptionTag',
    ];

    /**
     * @var array
     */
    protected $argumentDescriptions = [];

    /**
     * @var array
     */
    protected $optionDescriptions = [];

    /**
     * @var array
     */
    protected $exampleUsage = [];

    /**
     * @var array
     */
    protected $otherAnnotations = [];

    /**
     * @var array
     */
    protected $aliases = [];

    public function __construct($className, $methodName)
    {
        $this->reflection = new \ReflectionMethod($className, $methodName);
    }

    /**
     * Get the synopsis of the command (~first line).
     */
    public function getDescription()
    {
        $this->parseDocBlock();
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get the help text of the command (the description)
     */
    public function getHelp()
    {
        $parsed = $this->parseDocBlock();
        return $this->help;
    }

    public function setHelp($help)
    {
        $this->help = $help;
    }

    public function getAliases()
    {
        return $this->aliases;
    }

    public function setAliases($aliases)
    {
        if (is_string($aliases)) {
            $aliases = explode(',', static::convertListToCommaSeparated($aliases));
        }
        $this->aliases = array_filter($aliases);
    }

    public function getExampleUsages()
    {
        return $this->exampleUsage;
    }

    public function getName()
    {
        $name = $this->getAnnotation('name');
        if (!$name) {
            $name = $this->reflection->getName();
        }
        $name = $this->convertName($name);
        return $name;
    }

    public function getArguments()
    {
        $args = [];
        $params = $this->reflection->getParameters();
        foreach ($params as $key => $param) {

            // last array value is option, not argument
            if (($key == count($params)-1) and $param->isDefaultValueAvailable()) {
                if ($this->isAssoc($param->getDefaultValue())) break;
            }

            // arrays are array arguments
            if ($param->isArray()) {
                if ($param->isDefaultValueAvailable()) {
                    if (!$this->isAssoc($param->getDefaultValue())) $args[$param->getName()] = $param->getDefaultValue();
                } else {
                    $args[$param->getName()] = [];
                }
                continue;
            }

            // default values are optional arguments
            $val = $param->isDefaultValueAvailable()
                ? $param->getDefaultValue()
                : self::PARAM_IS_REQUIRED;

            $args[$param->getName()] = $val;
        }
        return $args;
    }

    public function getOptions()
    {
        $params = $this->reflection->getParameters();
        if (empty($params)) return [];
        $param = end($params);
        if (!$param->isDefaultValueAvailable()) return [];
        if (!$this->isAssoc($param->getDefaultValue())) return [];
        return $param->getDefaultValue();
    }

    public function getArgumentDescription($name)
    {
        $this->parseDocBlock();
        if (array_key_exists($name, $this->argumentDescriptions)) {
            return $this->argumentDescriptions[$name];
        }

        return '';
    }

    public function getOptionDescription($name)
    {
        $this->parseDocBlock();

        if (array_key_exists($name, $this->optionDescriptions)) {
            return $this->optionDescriptions[$name];
        }

        return '';
    }

    protected function isAssoc($arr)
    {
        if (!is_array($arr)) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    protected function getAnnotation($annotation)
    {
        $this->parseDocBlock();
        if (!array_key_exists($annotation, $this->otherAnnotations)) {
            return null;
        }
        return $this->otherAnnotations[$annotation];
    }

    protected function convertName($camel)
    {
        $splitter="-";
        $camel=preg_replace('/(?!^)[[:upper:]][[:lower:]]/', '$0', preg_replace('/(?!^)[[:upper:]]+/', $splitter.'$0', $camel));
        $camel = preg_replace("/$splitter/", ':', $camel, 1);
        return strtolower($camel);
    }

    /**
     * Parse the docBlock comment for this command, and set the
     * fields of this class with the data thereby obtained.
     */
    protected function parseDocBlock()
    {
        if (!$this->docBlockIsParsed) {
            $docblock = $this->reflection->getDocComment();
            $phpdoc = new DocBlock($docblock);

            // First set the description (synopsis) and help.
            $this->setDescription((string)$phpdoc->getShortDescription());
            $this->setHelp((string)$phpdoc->getLongDescription());

            // Iterate over all of the tags, and process them as necessary.
            foreach ($phpdoc->getTags() as $tag) {
                $processFn = [$this, 'processGenericTag'];
                if (array_key_exists($tag->getName(), $this->tagProcessors)) {
                    $processFn = [$this, $this->tagProcessors[$tag->getName()]];
                }
                $processFn($tag);
            }
            $this->docBlockIsParsed = true;
        }
    }

    /**
     * Save any tag that we do not explicitly recognize in the
     * 'otherAnnotations' map.
     */
    protected function processGenericTag($tag)
    {
        $this->otherAnnotations[$tag->getName()] = $tag->getContent();
    }

    /**
     * The @description and @desc annotations may be used in
     * place of the synopsis (which we call 'description').
     * This is discouraged.
     *
     * @deprecated
     */
    protected function processAlternateDescriptionTag($tag)
    {
        $this->setDescription($tag->getContent());
    }

    /**
     * Store the data from a @param annotation in our argument descriptions.
     */
    protected function processArgumentTag($tag)
    {
        if ($tag instanceof ParamTag) {
            $variableName = $tag->getVariableName();
            $variableName = str_replace('$', '', $variableName);
            $this->argumentDescriptions[$variableName] = static::removeLineBreaks($tag->getDescription());
        }
    }

    /**
     * Store the data from an @option annotation in our argument descriptions.
     */
    protected function processOptionTag($tag)
    {
        $name = '\\$(?P<name>[^ \t]+)[ \t]+';
        $description = '(?P<description>.*)';
        $optionRegEx = "/{$name}{$description}/s";

        if (preg_match($optionRegEx, $tag->getDescription(), $match)) {
            $this->optionDescriptions[$match['name']] = static::removeLineBreaks($match['description']);
        }
    }

    /**
     * Process the comma-separated list of aliases
     */
    protected function processAliases($tag)
    {
        $this->setAliases($tag->getDescription());
    }

    /**
     * Store the data from a @usage annotation in our example usage list.
     */
    protected function processUsageTag($tag)
    {
        $lines = explode("\n", $tag->getContent());
        $usage = array_shift($lines);
        $description = implode("\n", $lines);

        $this->exampleUsage[$usage] = $description;
    }

    /**
     * Given a list that might be 'a b c' or 'a, b, c' or 'a,b,c',
     * convert the data into the last of these forms.
     */
    protected static function convertListToCommaSeparated($text)
    {
        return preg_replace('#[ \t\n\r,]+#', ',', $text);
    }

    /**
     * Take a multiline description and convert it into a single
     * long unbroken line.
     */
    protected static function removeLineBreaks($text)
    {
        return trim(preg_replace('#[ \t\n\r]+#', ' ', $text));
    }
}

