<?php
namespace Robo;

class TaskInfo {

    const PARAM_IS_REQUIRED = '__param_is_required__';

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

    protected function isAssoc($arr)
    {
        if (!is_array($arr)) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
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
 