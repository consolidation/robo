<?php
class Robofile extends \Robo\Tasks
{
    use Robo\Task\GitHub;
    use Robo\Task\Changelog;

    public function release()
    {
        $this->say("Releasing Robo");

        $changes = $this->taskChangelog()
            ->version(\Robo\Runner::VERSION)
            ->askForChanges()
            ->run();

        $this->taskGitHubRelease(\Robo\Runner::VERSION)
            ->uri('Codegyre/Robo')
            ->askDescription()
            ->changes($changes)
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

}