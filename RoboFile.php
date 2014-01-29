<?php
class Robofile extends \Robo\Tasks
{
    use Robo\Task\GitHub;
    use Robo\Task\Changelog;
    use Robo\Task\Watch;

    public function release()
    {
        $this->say("Releasing Robo");

        $changelog = $this->taskChangelog()
            ->version(\Robo\Runner::VERSION)
            ->askForChanges();
        $changelog->run();
        
        $this->taskExec('git add CHANGELOG.md')->run();
        $this->taskExec('git commit -m "updated changelog"')->run();
        $this->taskExec('git push')->run();

        $this->taskGitHubRelease(\Robo\Runner::VERSION)
            ->uri('Codegyre/Robo')
            ->askDescription()
            ->changes($changelog->getChanges())
            ->run();
    }

    public function versionBump($version = null)
    {
        if (!$version) {
            $versionParts = explode('.', \Robo\Runner::VERSION);
            $versionParts[count($versionParts)-1]++;
            $version = implode('.', $versionParts);
        }
        $this->taskReplaceInFile(__DIR__.'/src/Runner.php')
            ->from("VERSION = '".\Robo\Runner::VERSION."'")
            ->to("VERSION = '".$version."'")
            ->run();
    }

    public function watch()
    {
        $this->taskWatch()->monitor('composer.json', function() {
            $this->taskComposerUpdate()->run();
        })->run();
    }
    
}