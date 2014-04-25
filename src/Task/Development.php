<?php
namespace Robo\Task;

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
 * Takes docblocks from classes and methods and generates markdown file.
 *
 * ``` php
 * $this->taskGenDoc('models.md')
 *      ->docClass('Model\User')
 *      ->docClass('Model\Post')
 *      ->filterMethods(function(\ReflectionMethod $r) {
 *          return $r->isPublic(); // process only public methods
 *      })->processClass(function(\ReflectionClass $r, $text) {
 *          return "Class ".$r->getName()."\n\n$text\n\n###Methods\n";
 *      })->run();
 * ```
 *
 * @method \Robo\Task\GenMarkdownDocTask docClass(string $classname)
 * @method \Robo\Task\GenMarkdownDocTask filterMethods(\Closure $func)
 * @method \Robo\Task\GenMarkdownDocTask filterClasses(\Closure $func)
 * @method \Robo\Task\GenMarkdownDocTask processMethod(\Closure $func)
 * @method \Robo\Task\GenMarkdownDocTask processClass(\Closure $func)
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
    protected $processMethod;
    protected $processClass;
    protected $reorder;
    protected $reorderMethods;
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

        $doc = self::indentDoc($refl->getDocComment());
        if (is_callable($this->processClass)) {
            $doc = call_user_func($this->processClass, $refl, $doc);
        } else {
            $doc = "## " . $refl->getName() . "\n\n$doc\n### Methods\n\n";
        }

        $methods = [];
        foreach ($refl->getMethods() as $reflMethod)
        {
            if (is_callable($this->filterMethods)) {
                $ret = call_user_func($this->filterMethods, $reflMethod);
                if (!$ret) continue;
            } else {
                if (!$reflMethod->isPublic()) continue;
            }
            $methodDoc = $reflMethod->getDocComment();
            // take from parent
            if (!$methodDoc) {
                $parent = $reflMethod->getDeclaringClass()->getParentClass();
                if ($parent && $parent->hasMethod($reflMethod->name)) {
                    $methodDoc = $parent->getMethod($reflMethod->name)->getDocComment();
                }
            }
            // take from interface
            if (!$methodDoc) {
                $interfaces = $reflMethod->getDeclaringClass()->getInterfaces();
                foreach ($interfaces as $interface) {
                    $i = new \ReflectionClass($interface->name);
                    if ($i->hasMethod($reflMethod->name)) {
                        $methodDoc = $i->getMethod($reflMethod->name)->getDocComment();
                        break;
                    }
                }
            }

            $methodDoc = self::indentDoc($methodDoc, 7);
            if (is_callable($this->processMethod)) {
                $methodDoc = call_user_func($this->processMethod, $reflMethod, $methodDoc);
            } else {
                $modifiers = implode(' ', \Reflection::getModifierNames($reflMethod->getModifiers()));
                $text = preg_replace("~@(.*?)([$\s])~",' * `$1` $2', $methodDoc); // format annotations
                $methodDoc = "#### *$modifiers* {$reflMethod->name}\n$methodDoc\n";
            }
            $methods[$reflMethod->name] = $methodDoc;
        }
        if (is_callable($this->reorderMethods)) {
            call_user_func_array($this->reorderMethods, [&$methods]);
        }
        $doc .= implode("\n",$methods);

        return $doc;
    }
    
    public static function indentDoc($doc, $indent = 3)
    {
        if (!$doc) return $doc;
        return implode("\n", array_map(function ($line) use ($indent)
                { return substr($line, $indent);
            }, explode("\n", $doc))
        );        
    }

}