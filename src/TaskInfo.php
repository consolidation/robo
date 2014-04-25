<?php
namespace Robo;

class TaskInfo {

    const PARAM_IS_REQUIRED = '__param_is_optional__';

    protected static $annotationRegex = '/@%s(?:[ \t]+(.*?))?[ \t]*\r?$/m';
    /**
     * @var \ReflectionMethod
     */
    protected $reflection;

    public function __construct($className, $methodName)
    {
        $this->reflection = new \ReflectionMethod($className, $methodName);
    }

    public function getDescription()
    {
        $desc = $this->getAnnotation('description');
        if (!$desc) {
            $desc = $this->getAnnotation('desc');
        }
        if (!$desc) {
            $doc = $this->reflection->getDocComment();
            $lines = explode(' *', $doc);
            if (isset($lines[1])) {
                $desc = trim($lines[1]);
            }
        }
        return $desc;
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
        foreach ($params as $param) {
            if ($param->isArray()) {
                continue;
            }
            $val = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : self::PARAM_IS_REQUIRED;
            $args[$param->getName()] = $val;
        }
        return $args;
    }

    protected function getAnnotation($annotation)
    {
        $docBlock = $this->reflection->getDocComment();
        $matched = array();
        $res = preg_match(sprintf(self::$annotationRegex, $annotation), $docBlock, $matched);
        if (!$res) return null;
        return $matched[1];
    }

    private function convertName($camel,$splitter=":")
    {
        $camel=preg_replace('/(?!^)[[:upper:]][[:lower:]]/', '$0', preg_replace('/(?!^)[[:upper:]]+/', $splitter.'$0', $camel));
        return strtolower($camel);
    }

}
 