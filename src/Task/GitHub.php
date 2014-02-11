<?php
namespace Robo\Task;

use Robo\Result;
use Symfony\Component\Console\Helper\DialogHelper;
const GITHUB_URL = 'https://api.github.com';

trait GitHub
{
    /**
     * @param $tag
     * @return GitHubReleaseTask
     */
    protected function taskGitHubRelease($tag)
    {
        return new GitHubReleaseTask($tag);
    }
}

/**
 * @method GitHubTask repo(string)
 * @method GitHubTask owner(string)
 */
abstract class GitHubTask implements TaskInterface
{
    use \Robo\Output;
    use DynamicConfig;

    protected static $user;
    protected static $pass;

    protected $needs_auth = false;
    protected $repo;
    protected $owner;

    public function uri($uri)
    {
        list($this->owner, $this->repo) = explode('/', $uri);
        return $this;
    }
    
    protected function getUri()
    {
        return $this->owner .'/'. $this->repo;
    }
    
    public function askAuth()
    {
        $dialog = new DialogHelper();
        self::$user = $dialog->ask($this->getOutput(), "<question>GitHub User</question>");
        self::$pass = $dialog->askHiddenResponse($this->getOutput(), "   <question>Password</question>");
    }

    protected function sendRequest($uri, $params = [], $method = 'POST')
    {
        if (!$this->owner or !$this->repo) {
            throw new TaskException($this, 'Repo URI is not set');
        }
        if ($this->needs_auth) {
            $this->askAuth();
        }
        $ch = curl_init();
        $url =  sprintf('%s/repos/%s/%s', GITHUB_URL, $this->getUri(), $uri);
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => $method != 'GET',
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => self::$user ?: "Robo"
        ));

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $output = curl_exec($ch);
        $response = json_decode($output);

        if ($code == 404 and !self::$user) {
            $this->askAuth();
            curl_setopt($ch, CURLOPT_USERPWD, self::$user.':'.self::$pass);
            $output = curl_exec($ch);
            $response = json_decode($output);
        }
        $this->printTaskInfo($output);
        return [$code, $response];
    }
}

/**
 * @method GitHubReleaseTask tag(string)
 * @method GitHubReleaseTask name(string)
 * @method GitHubReleaseTask body(string)
 * @method GitHubReleaseTask draft(boolean)
 * @method GitHubReleaseTask prerelease(boolean)
 * @method GitHubReleaseTask comittish(string)
 *
 * Class GitHubReleaseTask
 * @package Robo\Task
 */
class GitHubReleaseTask extends GitHubTask implements TaskInterface
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
        $this->body .= $this->ask("Description of Release\n")."\n\n";
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
        list($code, $data) = $this->sendRequest('releases', [
            "tag_name" => $this->tag,
            "target_commitish" => $this->comittish,
            "name" => $this->tag,
            "body" => $this->body,
            "draft" => $this->draft,
            "prerelease" => $this->prerelease
        ]);

        return new Result(
            $this,
            in_array($code, [200, 201]) ? 0 : 1,
            isset($data->message) ? $data->message : '',
            $data
        );
    }
}