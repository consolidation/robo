<?php
namespace Robo\Task\Vcs;

use Robo\Result;
use Robo\Task\string;
use Robo\Task\Vcs;
use Robo\Task\boolean;
use Robo\Contract\TaskInterface;

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
 * @method Vcs\GitHubRelease tag(string $tag)
 * @method Vcs\GitHubRelease name(string $name)
 * @method Vcs\GitHubRelease body(string $body)
 * @method Vcs\GitHubRelease draft(boolean $isDraft)
 * @method Vcs\GitHubRelease prerelease(boolean $isPrerelease)
 * @method Vcs\GitHubRelease comittish(string $branch)
 */
class GitHubRelease extends Vcs\GitHub implements TaskInterface
{
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

        return new Result(
            $this,
            in_array($code, [200, 201]) ? 0 : 1,
            isset($data->message) ? $data->message : '',
            $data
        );
    }
}