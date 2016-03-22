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
    protected $body = '';
    protected $changes = [];
    protected $draft = false;
    protected $prerelease = false;
    protected $comittish = 'master';

    public function __construct($tag)
    {
        $this->tag = $tag;
    }

    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    public function description($description)
    {
        $this->description = $description;
        return $this;
    }

    public function appendDescription($description)
    {
        if (!empty($this->description)) {
            $$this->description .= "\n\n";
        }
        $this->description .= $description;
        return $this;
    }

    public function changes(array $changes)
    {
        $this->changes = array_merge($this->changes, $changes);
        return $this;
    }

    public function change(string $change)
    {
        $this->changes[] = $change;
        return $this;
    }

    protected function getBody()
    {
        $body = $this->description;
        if (!empty($changes)) {
            $changes = array_map(function ($line) { return "* $line"; }, $this->changes);
            $changesText = implode("\n", $this->changes);
            $body .= "### Changelog \n\n$changesText";
        }
        return $body;
    }

    public function run()
    {
        $this->printTaskInfo('Releasing {tag}', ['tag' => $this->tag]);
        $this->startTimer();
        list($code, $data) = $this->sendRequest(
            'releases',
            [
                "tag_name" => $this->tag,
                "target_commitish" => $this->comittish,
                "name" => $this->tag,
                "body" => $this->getBody(),
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
