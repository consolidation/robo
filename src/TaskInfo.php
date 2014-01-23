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
        return $this->getAnnotation('description');
    }

    public function getName()
    {
        $name = $this->getAnnotation('name');
        if (!$name) {
            $name = $this->reflection->getName();
        }
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


}
 