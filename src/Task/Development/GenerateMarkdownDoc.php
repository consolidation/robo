<?php
namespace Robo\Task\Development;

use Robo\Task\BaseTask;
use Robo\Task\File\Write;
use Robo\Task\FileSystem;
use Robo\Result;
use Robo\Task\Development;

/**
 * Simple documentation generator from source files.
 * Takes classes, properties and methods with their docblocks and writes down a markdown file.
 *
 * ``` php
 * <?php
 * $this->taskGenerateMarkdownDoc('models.md')
 *      ->docClass('Model\User') // take class Model\User
 *      ->docClass('Model\Post') // take class Model\Post
 *      ->filterMethods(function(\ReflectionMethod $r) {
 *          return $r->isPublic() or $r->isProtected(); // process public and protected methods
 *      })->processClass(function(\ReflectionClass $r, $text) {
 *          return "Class ".$r->getName()."\n\n$text\n\n###Methods\n";
 *      })->run();
 * ```
 *
 * By default this task generates a documentation for each public method of a class.
 * It combines method signature with a docblock. Both can be post-processed.
 *
 * ``` php
 * <?php
 * $this->taskGenerateMarkdownDoc('models.md')
 *      ->docClass('Model\User')
 *      ->processClassSignature(false) // false can be passed to not include class signature
 *      ->processClassDocBlock(function(\ReflectionClass $r, $text) {
 *          return "[This is part of application model]\n" . $text;
 *      })->processMethodSignature(function(\ReflectionMethod $r, $text) {
 *          return "#### {$r->name}()";
 *      })->processMethodDocBlock(function(\ReflectionMethod $r, $text) {
 *          return strpos($r->name, 'save')===0 ? "[Saves to the database]\n" . $text : $text;
 *      })->run();
 * ```
 *
 * @method \Robo\Task\Development\GenerateMarkdownDoc docClass(string $classname) put a class you want to be documented
 * @method \Robo\Task\Development\GenerateMarkdownDoc filterMethods(\Closure $func) using callback function filter out methods that won't be documented
 * @method \Robo\Task\Development\GenerateMarkdownDoc filterClasses(\Closure $func) using callback function filter out classes that won't be documented
 * @method \Robo\Task\Development\GenerateMarkdownDoc filterProperties(\Closure $func) using callback function filter out properties that won't be documented
 * @method \Robo\Task\Development\GenerateMarkdownDoc processClass(\Closure $func) post-process class documentation
 * @method \Robo\Task\Development\GenerateMarkdownDoc processClassSignature(\Closure $func) post-process class signature. Provide *false* to skip.
 * @method \Robo\Task\Development\GenerateMarkdownDoc processClassDocBlock(\Closure $func) post-process class docblock contents. Provide *false* to skip.
 * @method \Robo\Task\Development\GenerateMarkdownDoc processMethod(\Closure $func) post-process method documentation. Provide *false* to skip.
 * @method \Robo\Task\Development\GenerateMarkdownDoc processMethodSignature(\Closure $func) post-process method signature. Provide *false* to skip.
 * @method \Robo\Task\Development\GenerateMarkdownDoc processMethodDocBlock(\Closure $func) post-process method docblock contents. Provide *false* to skip.
 * @method \Robo\Task\Development\GenerateMarkdownDoc processProperty(\Closure $func) post-process property documentation. Provide *false* to skip.
 * @method \Robo\Task\Development\GenerateMarkdownDoc processPropertySignature(\Closure $func) post-process property signature. Provide *false* to skip.
 * @method \Robo\Task\Development\GenerateMarkdownDoc processPropertyDocBlock(\Closure $func) post-process property docblock contents. Provide *false* to skip.
 * @method \Robo\Task\Development\GenerateMarkdownDoc reorder(\Closure $func) use a function to reorder classes
 * @method \Robo\Task\Development\GenerateMarkdownDoc reorderMethods(\Closure $func) use a function to reorder methods in class
 * @method \Robo\Task\Development\GenerateMarkdownDoc prepend($text) inserts text into beginning of markdown file
 * @method \Robo\Task\Development\GenerateMarkdownDoc append($text) inserts text in the end of markdown file
 */
class GenerateMarkdownDoc extends BaseTask
{
    use \Robo\Common\DynamicParams;

    protected $docClass = [];
    protected $filterMethods;
    protected $filterClasses;
    protected $filterProperties;

    // process class
    protected $processClass;
    protected $processClassSignature;
    protected $processClassDocBlock;

    // process methods
    protected $processMethod;
    protected $processMethodSignature;
    protected $processMethodDocBlock;

    // process Properties
    protected $processProperty;
    protected $processPropertySignature;
    protected $processPropertyDocBlock;

    protected $reorder;
    protected $reorderMethods;
    protected $reorderProperties;

    protected $filename;
    protected $prepend = "";
    protected $append = "";

    protected $text;
    protected $textForClass = [];

    /**
     * @param $filename
     * @return static
     */
    public static function init($filename)
    {
        return new static($filename);
    }

    function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function run()
    {
        foreach ($this->docClass as $class) {
            $this->printTaskInfo("Processing $class");
            $this->textForClass[$class] = $this->documentClass($class);
        }

        if (is_callable($this->reorder)) {
            $this->printTaskInfo("Applying reorder function");
            call_user_func_array($this->reorder, [$this->textForClass]);
        }

        $this->text = implode("\n", $this->textForClass);

        $result = (new Write($this->filename))
            ->line($this->prepend)
            ->text($this->text)
            ->line($this->append)
            ->run();

        $this->printTaskSuccess("<info>{$this->filename}</info> created. ".count($this->docClass)." classes documented");

        return new Result($this, $result->getExitCode(), $result->getMessage(), $this->textForClass);
    }

    protected function documentClass($class)
    {
        if (!class_exists($class)) {
            return "";
        }
        $refl = new \ReflectionClass($class);

        if (is_callable($this->filterClasses)) {
            $ret = call_user_func($this->filterClasses, $refl);
            if (!$ret) {
                return;
            }
        }
        $doc = $this->documentClassSignature($refl);
        $doc .= "\n" . $this->documentClassDocBlock($refl);
        $doc .= "\n";

        if (is_callable($this->processClass)) {
            $doc = call_user_func($this->processClass, $refl, $doc);
        }

        $properties = [];
        foreach ($refl->getProperties() as $reflProperty) {
            $properties[] = $this->documentProperty($reflProperty);
        }

        $properties = array_filter($properties);
        $doc .= implode("\n", $properties);

        $methods = [];
        foreach ($refl->getMethods() as $reflMethod) {
            $methods[$reflMethod->name] = $this->documentMethod($reflMethod);
        }
        if (is_callable($this->reorderMethods)) {
            call_user_func_array($this->reorderMethods, [&$methods]);
        }

        $methods = array_filter($methods);

        $doc .= implode("\n", $methods)."\n";

        return $doc;
    }

    protected function documentClassSignature(\ReflectionClass $reflectionClass)
    {
        if ($this->processClassSignature === false) {
            return "";
        }

        $signature = "## {$reflectionClass->name}\n\n";

        if ($parent = $reflectionClass->getParentClass()) {
            $signature .= "* *Extends* `{$parent->name}`";
        }
        $interfaces = $reflectionClass->getInterfaceNames();
        if (count($interfaces)) {
            $signature .= "\n* *Implements* `" . implode('`, `', $interfaces) . '`';
        }
        $traits = $reflectionClass->getTraitNames();
        if (count($traits)) {
            $signature .= "\n* *Uses* `" . implode('`, `', $traits) . '`';
        }
        if (is_callable($this->processClassSignature)) {
            $signature = call_user_func($this->processClassSignature, $reflectionClass, $signature);
        }

        return $signature;
    }

    protected function documentClassDocBlock(\ReflectionClass $reflectionClass)
    {
        if ($this->processClassDocBlock === false) {
            return "";
        }
        $doc = self::indentDoc($reflectionClass->getDocComment());
        if (is_callable($this->processClassDocBlock)) {
            $doc = call_user_func($this->processClassDocBlock, $reflectionClass, $doc);
        }
        return $doc;
    }

    protected function documentMethod(\ReflectionMethod $reflectedMethod)
    {
        if ($this->processMethod === false) {
            return "";
        }
        if (is_callable($this->filterMethods)) {
            $ret = call_user_func($this->filterMethods, $reflectedMethod);
            if (!$ret) {
                return "";
            }
        } else {
            if (!$reflectedMethod->isPublic()) {
                return "";
            }
        }

        $signature = $this->documentMethodSignature($reflectedMethod);
        $docblock = $this->documentMethodDocBlock($reflectedMethod);
        $methodDoc = "$signature $docblock";
        if (is_callable($this->processMethod)) {
            $methodDoc = call_user_func($this->processMethod, $reflectedMethod, $methodDoc);
        }
        return $methodDoc;
    }

    protected function documentProperty(\ReflectionProperty $reflectedProperty)
    {
        if ($this->processProperty === false) {
            return "";
        }
        if (is_callable($this->filterProperties)) {
            $ret = call_user_func($this->filterProperties, $reflectedProperty);
            if (!$ret) {
                return "";
            }
        } else {
            if (!$reflectedProperty->isPublic()) {
                return "";
            }
        }
        $signature = $this->documentPropertySignature($reflectedProperty);
        $docblock = $this->documentPropertyDocBlock($reflectedProperty);
        $propertyDoc = $signature . $docblock;
        if (is_callable($this->processProperty)) {
            $propertyDoc = call_user_func($this->processProperty, $reflectedProperty, $propertyDoc);
        }
        return $propertyDoc;
    }

    protected function documentPropertySignature(\ReflectionProperty $reflectedProperty)
    {
        if ($this->processPropertySignature === false) {
            return "";
        }
        $modifiers = implode(' ', \Reflection::getModifierNames($reflectedProperty->getModifiers()));
        $signature = "#### *$modifiers* {$reflectedProperty->name}";
        if (is_callable($this->processPropertySignature)) {
            $signature = call_user_func($this->processPropertySignature, $reflectedProperty, $signature);
        }
        return $signature;
    }

    protected function documentPropertyDocBlock(\ReflectionProperty $reflectedProperty)
    {
        if ($this->processPropertyDocBlock === false) {
            return "";
        }
        $propertyDoc = $reflectedProperty->getDocComment();
        // take from parent
        if (!$propertyDoc) {
            $parent = $reflectedProperty->getDeclaringClass();
            while ($parent = $parent->getParentClass()) {
                if ($parent->hasProperty($reflectedProperty->name)) {
                    $propertyDoc = $parent->getProperty($reflectedProperty->name)->getDocComment();
                }
            }
        }
        $propertyDoc = self::indentDoc($propertyDoc, 7);
        $propertyDoc = preg_replace("~^@(.*?)([$\s])~", ' * `$1` $2', $propertyDoc); // format annotations
        if (is_callable($this->processPropertyDocBlock)) {
            $propertyDoc = call_user_func($this->processPropertyDocBlock, $reflectedProperty, $propertyDoc);
        }
        return trim($propertyDoc);

    }

    protected function documentParam(\ReflectionParameter $param)
    {
        $text = "";
        if ($param->isArray()) {
            $text .= 'array ';
        }
        if ($param->isCallable()) {
            $text .= 'callable ';
        }
        $text .= '$' . $param->getName();
        if ($param->isDefaultValueAvailable()) {
            if ($param->allowsNull()) {
                $text .= ' = null';
            } else {
                $text .= ' = ' . str_replace("\n", ' ', print_r($param->getDefaultValue(), true));
            }
        }

        return $text;
    }

    public static function indentDoc($doc, $indent = 3)
    {
        if (!$doc) {
            return $doc;
        }
        return implode(
            "\n", array_map(
                function ($line) use ($indent) {
                    return substr($line, $indent);
                }, explode("\n", $doc)
            )
        );
    }

    protected function documentMethodSignature(\ReflectionMethod $reflectedMethod)
    {
        if ($this->processMethodSignature === false) {
            return "";
        }
        $modifiers = implode(' ', \Reflection::getModifierNames($reflectedMethod->getModifiers()));
        $params = implode(
            ', ', array_map(
                function ($p) {
                    return $this->documentParam($p);
                }, $reflectedMethod->getParameters()
            )
        );
        $signature = "#### *$modifiers* {$reflectedMethod->name}($params)";
        if (is_callable($this->processMethodSignature)) {
            $signature = call_user_func($this->processMethodSignature, $reflectedMethod, $signature);
        }
        return $signature;
    }

    /**
     * @param \ReflectionMethod $reflectedMethod
     * @return mixed|string
     */
    protected function documentMethodDocBlock(\ReflectionMethod $reflectedMethod)
    {
        if ($this->processMethodDocBlock === false) {
            return "";
        }
        $methodDoc = $reflectedMethod->getDocComment();
        // take from parent
        if (!$methodDoc) {
            $parent = $reflectedMethod->getDeclaringClass();
            while ($parent = $parent->getParentClass()) {
                if ($parent->hasMethod($reflectedMethod->name)) {
                    $methodDoc = $parent->getMethod($reflectedMethod->name)->getDocComment();
                }
            }
        }
        // take from interface
        if (!$methodDoc) {
            $interfaces = $reflectedMethod->getDeclaringClass()->getInterfaces();
            foreach ($interfaces as $interface) {
                $i = new \ReflectionClass($interface->name);
                if ($i->hasMethod($reflectedMethod->name)) {
                    $methodDoc = $i->getMethod($reflectedMethod->name)->getDocComment();
                    break;
                }
            }
        }

        $methodDoc = self::indentDoc($methodDoc, 7);
        $methodDoc = preg_replace("~^@(.*?) ([$\s])~m", ' * `$1` $2', $methodDoc); // format annotations
        if (is_callable($this->processMethodDocBlock)) {
            $methodDoc = call_user_func($this->processMethodDocBlock, $reflectedMethod, $methodDoc);
        }

        return $methodDoc;
    }

}