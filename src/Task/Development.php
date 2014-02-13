<?php
namespace Robo\Task;

use Robo\Output;
use Robo\Result;

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
 * @method ChangelogTask filename(string $filename)
 * @method ChangelogTask anchor(string $anchor)
 * @method ChangelogTask version(string $version)
 */
class ChangelogTask implements TaskInterface
{
    use \Robo\Output;
    use FileSystem;
    use DynamicConfig;

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
 * @method GenMarkdownDocTask docClass(string $classname)
 * @method GenMarkdownDocTask filterMethods(\Closure $func)
 * @method GenMarkdownDocTask filterClasses(\Closure $func)
 * @method GenMarkdownDocTask processMethod(\Closure $func)
 * @method GenMarkdownDocTask processClass(\Closure $func)
 * @method GenMarkdownDocTask reorder(\Closure $func)
 * @method GenMarkdownDocTask prepend(string $text)
 * @method GenMarkdownDocTask append(string $text)
 */
class GenMarkdownDocTask implements TaskInterface
{
    use DynamicConfig;
    use Output;
    use FileSystem;

    protected $docClass = [];
    protected $filterMethods;
    protected $filterClasses;
    protected $processMethod;
    protected $processClass;
    protected $reorder;
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
            $this->textForClass = call_user_func($this->reorder, $this->textForClass);
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
        $refl = new \ReflectionClass($class);

        if (is_callable($this->filterClasses)) {
            $ret = call_user_func($this->filterClasses, $refl);
            if (!$ret) return;
        }

        $doc = $this->indentDoc($refl->getDocComment());
        if (is_callable($this->processClass)) {
            $doc = call_user_func($this->processClass, $refl, $doc);
        } else {
            $doc = "## " . $refl->getName() . "\n\n$doc\n### Methods\n\n";
        }

        foreach ($refl->getMethods() as $reflMethod)
        {
            if (is_callable($this->filterMethods)) {
                $ret = call_user_func($this->filterMethods, $reflMethod);
                if (!$ret) continue;
            }

            $methodDoc = $this->indentDoc($reflMethod->getDocComment());
            if (is_callable($this->processMethod)) {
                $methodDoc = call_user_func($this->processMethod, $reflMethod, $methodDoc);
            } else {
                $methodDoc = "### Method \n\n$methodDoc\n";
            }
            $doc .= $methodDoc;
        }
        return $doc;
    }
    
    protected function indentDoc($doc, $indent = 3)
    {
        return implode("\n", array_map(function ($line) use ($indent)
                { return substr($line, $indent);
            }, explode("\n", $doc))
        );        
    }

}