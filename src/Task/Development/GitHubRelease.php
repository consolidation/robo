<?php
namespace Robo\Task\Development;

use Robo\Common\BuilderAwareTrait;
use Robo\Contract\BuilderAwareInterface;
use Robo\Result;

/**
 * Publishes new GitHub release.
 *
 * ``` php
 * <?php
 * $this->taskGitHubRelease('0.1.0')
 *   ->uri('consolidation-org/Robo')
 *   ->description('Add stuff people need.')
 *   ->change('Fix #123')
 *   ->change('Add frobulation method to all widgets')
 *   ->run();
 * ?>
 * ```
 */
class GitHubRelease extends GitHub implements BuilderAwareInterface
{
    use BuilderAwareTrait;

    /**
     * @var string
     */
    protected $tag;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string[]
     */
    protected $changes = [];

    /**
     * @var bool
     */
    protected $draft = false;

    /**
     * @var bool
     */
    protected $prerelease = false;

    /**
     * @var bool
     */
    protected $openBrowser = false;

    /**
     * @var string
     */
    protected $comittish = 'master';

    /**
     * @param string $tag
     */
    public function __construct($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @param string $tag
     *
     * @return $this
     */
    public function tag($tag)
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * @param bool $draft
     *
     * @return $this
     */
    public function draft($draft)
    {
        $this->draft = $draft;
        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function description($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param bool $prerelease
     *
     * @return $this
     */
    public function prerelease($prerelease)
    {
        $this->prerelease = $prerelease;
        return $this;
    }

    /**
     * @param string $comittish
     *
     * @return $this
     */
    public function comittish($comittish)
    {
        $this->comittish = $comittish;
        return $this;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function appendDescription($description)
    {
        if (!empty($this->description)) {
            $this->description .= "\n\n";
        }
        $this->description .= $description;
        return $this;
    }

    public function changes(array $changes)
    {
        $this->changes = array_merge($this->changes, $changes);
        return $this;
    }

    /**
     * @param string $change
     *
     * @return $this
     */
    public function change($change)
    {
        $this->changes[] = $change;
        return $this;
    }

    /**
     * @return string
     */
    protected function getBody()
    {
        $body = $this->description;
        if (!empty($this->changes)) {
            $changes = array_map(
                function ($line) {
                    return "* $line";
                },
                $this->changes
            );
            $changesText = implode("\n", $changes);
            $body .= "### Changelog \n\n$changesText";
        }
        return $body;
    }

    /**
     *
     * If true, a browser will open the new release after it is made.
     *
     * @param $open bool
     *
     * @return self
     */
    public function openBrowser($open = true)
    {
        $this->openBrowser = $open;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->printTaskInfo('Releasing {tag}', ['tag' => $this->tag]);
        $this->startTimer();
        list($code, $data) = $this->sendRequest(
            'releases',
            [
                "tag_name" => $this->tag,
                "target_commitish" => $this->comittish,
                "name" => $this->name,
                "body" => $this->getBody(),
                "draft" => $this->draft,
                "prerelease" => $this->prerelease
            ]
        );

        $exit_code = in_array($code, [200, 201]) ? 0 : 1;
        if (!$exit_code && $this->openBrowser) {
            $this->collectionBuilder()->taskOpenBrowser($data->html_url)->run();
        }

        $this->stopTimer();

        return new Result(
            $this,
            $exit_code,
            isset($data->message) ? $data->message : '',
            ['response' => $data, 'time' => $this->getExecutionTime()]
        );
    }
}
