<?php
namespace Robo\Task\Development;

use Robo\Common\Timer;
use Robo\Result;

/**
 * Publishes new GitHub release.
 *
 * ``` php
 * <?php
 * $this->taskGitHubRelease('0.1.0')
 *   ->uri('Codegyre/Robo')
 *   ->askDescription()
 *   ->run();
 * ?>
 * ```
 *
 * @method \Robo\Task\Development\GitHubRelease tag(string $tag)
 * @method \Robo\Task\Development\GitHubRelease name(string $name)
 * @method \Robo\Task\Development\GitHubRelease body(string $body)
 * @method \Robo\Task\Development\GitHubRelease draft(boolean $isDraft)
 * @method \Robo\Task\Development\GitHubRelease prerelease(boolean $isPrerelease)
 * @method \Robo\Task\Development\GitHubRelease comittish(string $branch)
 */
class GitHubRelease extends GitHub
{
    use Timer;

    protected $tag;
    protected $name;
    protected $body;
    protected $draft = false;
    protected $prerelease = false;
    protected $comittish = 'master';

    public function __construct($tag)
    {
        $this->tag = $tag;
    }

    public function askName()
    {
        $this->name = $this->ask("Release Title");
        return $this;
    }

    public function askDescription()
    {
        $this->body .= $this->ask("Description of Release\n") . "\n\n";
        return $this;
    }

    public function askForChanges()
    {
        $this->body .= "### Changelog \n\n";
        while ($resp = $this->ask("Added in this release:")) {
            $this->body .= "* $resp\n";
        };
        return $this;
    }

    public function changes(array $changes)
    {
        $this->body .= "### Changelog \n\n";
        foreach ($changes as $change) {
            $this->body .= "* $change\n";
        }
        return $this;
    }

    public function run()
    {
        $this->printTaskInfo("Releasing " . $this->tag);
        $this->startTimer();
        list($code, $data) = $this->sendRequest(
            'releases', [
                "tag_name" => $this->tag,
                "target_commitish" => $this->comittish,
                "name" => $this->tag,
                "body" => $this->body,
                "draft" => $this->draft,
                "prerelease" => $this->prerelease
            ]
        );
        $this->stopTimer();

        return new Result(
            $this,
            in_array($code, [200, 201]) ? 0 : 1,
            isset($data->message) ? $data->message : '',
            ['response' => $data, 'time' => $this->getExecutionTime()]
        );
    }
}