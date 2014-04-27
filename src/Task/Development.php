<?php
namespace Robo\Task;
trait_exists('Robo\Task\FileSystem', true);

use Robo\Output;
use Robo\Result;
use Robo\Task\Shared\TaskInterface;

/**
 * Contains simple tasks to simplify documenting of development process.
 * @package Robo\Task
 */
trait Development
{
    protected function taskChangelog($filename = 'CHANGELOG.md')
    {
        return new ChangelogTask($filename);
    }

    protected function taskGenDoc($filename)
    {
        return new GenMarkdownDocTask($filename);
    }
    
}

/**
 * Helps to manage changelog file.
 * Creates or updates `changelog.md` file with recent changes in current version.
 *
 * ``` php
 * <?php
 * $version = "0.1.0";
 * $this->taskChangelog()
 *  ->version($version)
 *  ->change("released to github")
 *  ->run();
 * ?>
 * ```
 *
 * Changes can be asked from Console
 *
 * ``` php
 * <?php
 * $this->taskChangelog()
 *  ->version($version)
 *  ->askForChanges()
 *  ->run();
 * ?>
 * ```
 *
 * @method \Robo\Task\ChangelogTask filename(string $filename)
 * @method \Robo\Task\ChangelogTask anchor(string $anchor)
 * @method \Robo\Task\ChangelogTask version(string $version)
 */
class ChangelogTask implements TaskInterface
{
    use Output;
    use Shared\DynamicConfig;
    use FileSystem;

    protected $filename;
    protected $log = [];
    protected $anchor = "# Changelog";
    protected $version = "";

    public function askForChanges()
    {
        while ($resp = $this->ask("Changed in this release: ")) {
            $this->log[] = $resp;
        };
        return $this;
    }

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function changes(array $data)
    {
        $this->log = array_merge($this->log, $data);
        return $this;
    }

    public function change($change)
    {
        $this->log[] = $change;
        return $this;
    }

    public function getChanges()
    {
        return $this->log;
    }

    public function run()
    {
        if (empty($this->log)) {
            return Result::error($this, "Changelog is empty");
        }
        $text = implode("\n", array_map(function ($i) { return "* $i"; }, $this->log))."\n";
        $ver = "#### {$this->version} ".date('m/d/Y')."\n\n";
        $text = $ver . $text;

        if (!file_exists($this->filename)) {
            $this->printTaskInfo("Creating {$this->filename}");
            $res = file_put_contents($this->filename, $this->anchor);
            if ($res === false) return Result::error($this, "File {$this->filename} cant be created");
        }

        // trying to append to changelog for today
        $result = (new ReplaceInFileTask($this->filename))
            ->from($ver)
            ->to($text)
            ->run();

        if (!$result->getData()['replaced']) {
            $result = (new ReplaceInFileTask($this->filename))
                ->from($this->anchor)
                ->to($this->anchor."\n\n".$text)
                ->run();
        }

        return new Result($this, $result->getExitCode(), $result->getMessage(), $this->log);
    }
}

/**
 * Simple documentation generator from source files.
 * Takes classes, properties and methods with their docblocks and writes down a markdown file.
 *
 * ``` php
 * $this->taskGenDoc('models.md')
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
 * $this->taskGenDoc('models.md')
 *      ->docClass('Model\User')
 *      })->processClassDocBlock(function(\ReflectionClass $r, $text) {
 *          return "[This is part of application model]\n" . $text;
 *      ))->processMethodSignature(function(\ReflectionMethod $r, $text) {
 *          return "#### {$r->name}()";
 *      ))->processMethodDocBlock(function(\ReflectionMethod $r, $text) {
 *          return strpos($r->name, 'save')===0 ? "[Saves to the database]\n" . $text : $text;
 *      })->run();
 * ```
 *
 * @method \Robo\Task\GenMarkdownDocTask docClass(string $classname)
 * @method \Robo\Task\GenMarkdownDocTask filterMethods(\Closure $func)
 * @method \Robo\Task\GenMarkdownDocTask filterClasses(\Closure $func)
 * @method \Robo\Task\GenMarkdownDocTask filterProperties(\Closure $func)
 * @method \Robo\Task\GenMarkdownDocTask processClass(\Closure $func)
 * @method \Robo\Task\GenMarkdownDocTask processClassSignature(\Closure $func)
 * @method \Robo\Task\GenMarkdownDocTask processClassDocBlock(\Closure $func)
 * @method \Robo\Task\GenMarkdownDocTask processMethod(\Closure $func)
 * @method \Robo\Task\GenMarkdownDocTask processMethodSignature(\Closure $func)
 * @method \Robo\Task\GenMarkdownDocTask processMethodDocBlock(\Closure $func)
 * @method \Robo\Task\GenMarkdownDocTask reorder(\Closure $func)
 * @method \Robo\Task\GenMarkdownDocTask reorderMethods(\Closure $func)
 * @method \Robo\Task\GenMarkdownDocTask prepend($text)
 * @method \Robo\Task\GenMarkdownDocTask append($text)
 */
class GenMarkdownDocTask implements TaskInterface
{
    use Shared\DynamicConfig;
    use Output;
    use FileSystem;

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

        $result = $this->taskWriteToFile($this->filename)
            ->line($this->prepend)            
            ->text($this->text)
            ->line($this->append)
            ->run();

        return new Result($this, $result->getExitCode(), $result->getMessage(), $this->textForClass);
    }

    protected function documentClass($class)
    {
        if (!class_exists($class)) return "";
        $refl = new \ReflectionClass($class);

        if (is_callable($this->filterClasses)) {
            $ret = call_user_func($this->filterClasses, $refl);
            if (!$ret) return;
        }
        $doc = $this->documentClassSignature($refl);
        $doc .= "\n".$this->documentClassDocBlock($refl);

        $properties = [];
        foreach ($refl->getProperties() as $reflProperty) {
            $properties[] = $this->documentProperty($reflProperty);
        }

        $doc .= implode("\n", $properties);

        $methods = [];
        foreach ($refl->getMethods() as $reflMethod)
        {
            $methods[] = $this->documentMethod($reflMethod);
        }
        if (is_callable($this->reorderMethods)) {
            call_user_func_array($this->reorderMethods, [&$methods]);
        }

        $doc .= implode("\n", $methods);

        return $doc;
    }

    protected function documentClassSignature(\ReflectionClass $reflectionClass)
    {
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
        $doc = self::indentDoc($reflectionClass->getDocComment());
        if (is_callable($this->processClassDocBlock)) {
            $doc = call_user_func($this->processClassDocBlock, $reflectionClass, $doc);
        }
        return $doc;
    }

    protected function documentMethod(\ReflectionMethod $reflectedMethod)
    {
        if (is_callable($this->filterMethods)) {
            $ret = call_user_func($this->filterMethods, $reflectedMethod);
            if (!$ret) return "";
        } else {
            if (!$reflectedMethod->isPublic()) return "";
        }

        $signature = $this->documentMethodSignature($reflectedMethod);
        $docblock = $this->documentMethodDocBlock($reflectedMethod);
        $methodDoc = $signature . $docblock;
        if (is_callable($this->processMethod)) {
            $methodDoc = call_user_func($this->processMethod, $reflectedMethod, $methodDoc);
        }
        return $methodDoc;
    }

    protected function documentProperty(\ReflectionProperty $reflectedProperty)
    {
        if (is_callable($this->filterProperties)) {
            $ret = call_user_func($this->filterProperties, $reflectedProperty);
            if (!$ret) return "";
        } else {
            if (!$reflectedProperty->isPublic()) return "";
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
        $modifiers = implode(' ', \Reflection::getModifierNames($reflectedProperty->getModifiers()));
        $signature = "#### *$modifiers* {$reflectedProperty->name}";
        if (is_callable($this->processPropertySignature)) {
            $signature = call_user_func($this->processPropertySignature, $reflectedProperty, $signature);
        }
        return $signature;
    }

    protected function documentPropertyDocBlock(\ReflectionProperty $reflectedProperty)
    {
        $propertyDoc = $reflectedProperty->getDocComment();
         // take from parent
         if (!$propertyDoc) {
             $parent = $reflectedProperty->getDeclaringClass();
             while($parent = $parent->getParentClass()) {
                 if ($parent->hasProperty($reflectedProperty->name)) {
                     $propertyDoc = $parent->getProperty($reflectedProperty->name)->getDocComment();
                 }
             }
         }
        $propertyDoc = self::indentDoc($propertyDoc, 7);
        $propertyDoc = preg_replace("~@(.*?)([$\s])~", ' * `$1` $2', $propertyDoc); // format annotations
        if (is_callable($this->processPropertyDocBlock)) {
            $propertyDoc = call_user_func($this->processPropertyDocBlock, $reflectedProperty, $propertyDoc);
        }
        return trim($propertyDoc);

    }

    protected function documentParam(\ReflectionParameter $param)
    {
        $text = "";
        if ($param->isArray()) $text .= 'array ';
        if ($param->isCallable()) $text .= 'callable ';
        $text .= '$'.$param->getName();
        if ($param->isDefaultValueAvailable()) {
            if ($param->allowsNull()) {
                $text .= ' = null';
            } else {
                $text .= ' = '.$param->getDefaultValue();
            }
        }

        return $text;
    }
    
    public static function indentDoc($doc, $indent = 3)
    {
        if (!$doc) return $doc;
        return implode("\n", array_map(function ($line) use ($indent)
                { return substr($line, $indent);
            }, explode("\n", $doc))
        );        
    }

    protected function documentMethodSignature(\ReflectionMethod $reflectedMethod)
    {
        $modifiers = implode(' ', \Reflection::getModifierNames($reflectedMethod->getModifiers()));
        $params = implode(', ', array_map(function ($p) {
            return $this->documentParam($p);
        }, $reflectedMethod->getParameters()));
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
        $methodDoc = $reflectedMethod->getDocComment();
        // take from parent
        if (!$methodDoc) {
            $parent = $reflectedMethod->getDeclaringClass();
            while($parent = $parent->getParentClass()) {
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
        $methodDoc = preg_replace("~@(.*?)([$\s])~", ' * `$1` $2', $methodDoc); // format annotations
        if (is_callable($this->processMethodDocBlock)) {
            $methodDoc = call_user_func($this->processMethodDocBlock, $reflectedMethod, $methodDoc);
        }

        return trim($methodDoc);
    }

}