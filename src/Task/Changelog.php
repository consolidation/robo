<?php
namespace Robo\Task;

trait Changelog
{
    protected function taskChangelog($filename = 'CHANGELOG.md')
    {
        return new ChangelogTask($filename);
    }
}

class ChangelogTask implements TaskInterface
{
    use \Robo\Output;
    use FileSystem;

    protected $filename;
    protected $log = [];
    protected $anchor = "# Changelog";
    protected $version = "";

    public function askForChanges()
    {
        while ($resp = $this->ask("Changed in this release:")) {
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
    
    public function anchor($anchor)
    {
        $this->anchor = $anchor;
        return $this;
    }

    public function getChanges()
    {
        return $this->log;
    }

    public function version($version)
    {
        $this->version = $version;
        return $this;
    }

    public function run()
    {
        if (empty($this->log)) {
            $this->printTaskInfo("<alert>Changelog is empty</alert>");
            return false;
        }
        $text = implode("\n", array_map(function ($i) { return "* $i"; }, $this->log));
        $text = "#### {$this->version} ".date('m/d/Y')."\n\n".$text;

        if (!file_exists($this->filename)) {
            $this->printTaskInfo("Creating {$this->filename}");
            file_put_contents($this->filename, $this->anchor);
        }

        return (new ReplaceInFileTask($this->filename))
            ->from($this->anchor)
            ->to($this->anchor."\n\n".$text)
            ->run();

    }

}