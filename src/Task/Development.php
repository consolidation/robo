<?php
namespace Robo\Task;

use Robo\Output;
use Robo\Result;

trait Development
{
    protected function taskChangelog($filename = 'CHANGELOG.md')
    {
        return new ChangelogTask($filename);
    }

    protected function taskGenDoc($filename)
    {
        return new GenMarkdownDoc($filename);
    }
    
}

/**
 * @method ChangelogTask filename(string $filename)
 * @method ChangelogTask anchor(string $anchor)
 * @method ChangelogTask version(string $version)
 *
 * @package Robo\Task
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
 * @method GenMarkdownDoc docClass($classname)
 * @method GenMarkdownDoc filterMethods(\Closure $func)
 * @method GenMarkdownDoc processMethod(\Closure $func)
 * @method GenMarkdownDoc processClass(\Closure $func)
 * @method GenMarkdownDoc reorder(\Closure $func)
 *
 * Class GenMarkdownDoc
 * @package Robo\Task
 */
class GenMarkdownDoc implements TaskInterface
{
    use DynamicConfig;
    use Output;
    use FileSystem;

    protected $docClass = [];
    protected $filterMethods;
    protected $processMethod;
    protected $processClass;
    protected $reorder;
    protected $filename;

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
            ->text($this->text)
            ->run();

        return new Result($this, $result->getExitCode(), $result->getMessage(), $this->textForClass);
    }

    protected function documentClass($class)
    {
        $refl = new \ReflectionClass($class);

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
            if (is_callable($this->processClass)) {
                $methodDoc = call_user_func($this->processClass, $reflMethod, $methodDoc);
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