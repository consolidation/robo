<?php
namespace Robo\Task;
trait_exists('Robo\Task\FileSystem', true);

use Robo\Output;
use Robo\Result;
use Robo\Task\Shared\TaskException;
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

     protected function taskSemVer($pathToSemVer = '.semver')
     {
         return new SemVerTask($pathToSemVer);
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
 * <?php
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
 * <?php
 * $this->taskGenDoc('models.md')
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
 * @method \Robo\Task\GenMarkdownDocTask docClass(string $classname) put a class you want to be documented
 * @method \Robo\Task\GenMarkdownDocTask filterMethods(\Closure $func) using callback function filter out methods that won't be documented
 * @method \Robo\Task\GenMarkdownDocTask filterClasses(\Closure $func) using callback function filter out classes that won't be documented
 * @method \Robo\Task\GenMarkdownDocTask filterProperties(\Closure $func) using callback function filter out properties that won't be documented
 * @method \Robo\Task\GenMarkdownDocTask processClass(\Closure $func) post-process class documentation
 * @method \Robo\Task\GenMarkdownDocTask processClassSignature(\Closure $func) post-process class signature. Provide *false* to skip.
 * @method \Robo\Task\GenMarkdownDocTask processClassDocBlock(\Closure $func) post-process class docblock contents. Provide *false* to skip.
 * @method \Robo\Task\GenMarkdownDocTask processMethod(\Closure $func) post-process method documentation. Provide *false* to skip.
 * @method \Robo\Task\GenMarkdownDocTask processMethodSignature(\Closure $func) post-process method signature. Provide *false* to skip.
 * @method \Robo\Task\GenMarkdownDocTask processMethodDocBlock(\Closure $func) post-process method docblock contents. Provide *false* to skip.
 * @method \Robo\Task\GenMarkdownDocTask processProperty(\Closure $func) post-process property documentation. Provide *false* to skip.
 * @method \Robo\Task\GenMarkdownDocTask processPropertySignature(\Closure $func) post-process property signature. Provide *false* to skip.
 * @method \Robo\Task\GenMarkdownDocTask processPropertyDocBlock(\Closure $func) post-process property docblock contents. Provide *false* to skip. 
 * @method \Robo\Task\GenMarkdownDocTask reorder(\Closure $func) use a function to reorder classes
 * @method \Robo\Task\GenMarkdownDocTask reorderMethods(\Closure $func) use a function to reorder methods in class
 * @method \Robo\Task\GenMarkdownDocTask prepend($text) inserts text into beginning of markdown file
 * @method \Robo\Task\GenMarkdownDocTask append($text) inserts text in the end of markdown file
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
        
        if (is_callable($this->processClass)) {
            $doc = call_user_func($this->processClass, $refl, $doc);
        }

        $properties = [];
        foreach ($refl->getProperties() as $reflProperty) {
            $properties[] = $this->documentProperty($reflProperty);
        }

        $doc .= implode("\n", $properties);

        $methods = [];
        foreach ($refl->getMethods() as $reflMethod) {
            $methods[$reflMethod->name] = $this->documentMethod($reflMethod);
        }
        if (is_callable($this->reorderMethods)) {
            call_user_func_array($this->reorderMethods, [&$methods]);
        }

        $doc .= implode("\n", $methods);

        return $doc;
    }

    protected function documentClassSignature(\ReflectionClass $reflectionClass)
    {
        if ($this->processClassSignature === false) return "";

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
        if ($this->processClassDocBlock === false) return "";
        $doc = self::indentDoc($reflectionClass->getDocComment());
        if (is_callable($this->processClassDocBlock)) {
            $doc = call_user_func($this->processClassDocBlock, $reflectionClass, $doc);
        }
        return $doc;
    }

    protected function documentMethod(\ReflectionMethod $reflectedMethod)
    {
        if ($this->processMethod === false) return "";
        if (is_callable($this->filterMethods)) {
            $ret = call_user_func($this->filterMethods, $reflectedMethod);
            if (!$ret) return "";
        } else {
            if (!$reflectedMethod->isPublic()) return "";
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
        if ($this->processProperty === false) return "";
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
        if ($this->processPropertySignature === false) return "";
        $modifiers = implode(' ', \Reflection::getModifierNames($reflectedProperty->getModifiers()));
        $signature = "#### *$modifiers* {$reflectedProperty->name}";
        if (is_callable($this->processPropertySignature)) {
            $signature = call_user_func($this->processPropertySignature, $reflectedProperty, $signature);
        }
        return $signature;
    }

    protected function documentPropertyDocBlock(\ReflectionProperty $reflectedProperty)
    {
        if ($this->processPropertyDocBlock === false) return "";
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
        if ($this->processMethodSignature === false) return "";
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
        if ($this->processMethodDocBlock === false) return "";
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

        return $methodDoc;
    }

}

/**
 * Helps to maintain `.semver` file.
 *
 * ```php
 * <?php
 * $this->taskSemVer('.semver')
 *      ->increment()
 *      ->run();
 * ?>
 * ```
 *
 */
class SemVerTask implements TaskInterface
{
    const SEMVER = "---\n:major: %d\n:minor: %d\n:patch: %d\n:special: '%s'\n:metadata: '%s'";

    const REGEX = "/^\-\-\-\n:major:\s(0|[1-9]\d*)\n:minor:\s(0|[1-9]\d*)\n:patch:\s(0|[1-9]\d*)\n:special:\s'([a-zA-z0-9]*\.?(?:0|[1-9]\d*)?)'\n:metadata:\s'((?:0|[1-9]\d*)?(?:\.[a-zA-z0-9\.]*)?)'/";

    protected $format = 'v%M.%m.%p%s';

    protected $specialSeparator = '-';

    protected $metadataSeparator = '+';

    protected $path;

    protected $version = [
        'major' => 0,
        'minor' => 0,
        'patch' => 0,
        'special' => '',
        'metadata' => ''
    ];

    public function __construct($pathToSemVer = '.semver')
    {
        $this->path = $pathToSemVer;

        if (file_exists($this->path)) {
            $this->parse();
        }
    }

    public function __toString()
    {
        $search = ['%M', '%m', '%p', '%s'];
        $replace = $this->version + ['extra' => ''];

        foreach (['special', 'metadata'] as $key) {
            if (!empty($replace[$key])) {
                $separator = $key . 'Separator';
                $replace['extra'] .= $this->{$separator} . $replace[$key];
            }
            unset($replace[$key]);
        }

        return str_replace($search, $replace, $this->format);
    }

    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    public function setMetadataSeparator($separator)
    {
        $this->metadataSeparator = $separator;
        return $this;
    }

    public function setPrereleaseSeparator($separator)
    {
        $this->specialSeparator = $separator;
        return $this;
    }

    public function increment($what = 'patch')
    {
        $types = ['major', 'minor', 'patch'];
        if (!in_array($what, $types)) {
            throw new TaskException(
                $this,
                'Bad argument, only one of the following is allowed: ' .
                implode(', ', $types)
            );
        }

        $this->version[$what]++;
        return $this;
    }

    public function prerelease($tag = 'RC')
    {
        if (!is_string($tag)) {
            throw new TaskExecption($this, 'Bad argument, only strings allowed.');
        }

        $number = 0;

        if (!empty($this->version['special'])) {
            list($current, $number) = explode('.', $this->version['special']);
            if ($tag != $current) {
                $number = 0;
            }
        }

        $number++;

        $this->version['special'] = implode('.', [$tag, $number]);
        return $this;
    }

    public function metadata($data)
    {
        if (is_array($data)) {
            $data = implode('.', $data);
        }

        $this->version['metadata'] = $data;
        return $this;
    }

    public function run()
    {
        $written = $this->dump();
        return new Result($this, (int)($written !== false),  $this->__toString());
    }

    protected function dump()
    {
        extract($this->version);
        $semver = sprintf(self::SEMVER, $major, $minor, $patch, $special, $metadata);
        return file_put_contents($this->path, $semver);
    }

    protected function parse()
    {
        $output = file_get_contents($this->path);

        if (!preg_match_all(self::REGEX, implode("\n", $output), $matches)) {
            throw new TaskException($this, 'Bad semver file.');
        }

        list(, $major, $minor, $patch, $special, $metadata) = array_map('current', $matches);
        $this->version = compact('major', 'minor', 'patch', 'special', 'metadata');
    }
}
