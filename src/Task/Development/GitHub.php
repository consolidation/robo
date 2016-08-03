<?php
namespace Robo\Task\Development;

use Robo\Exception\TaskException;
use Robo\Task\BaseTask;

/**
 * @method \Robo\Task\Development\GitHub repo(string)
 * @method \Robo\Task\Development\GitHub owner(string)
 */
abstract class GitHub extends BaseTask
{
    const GITHUB_URL = 'https://api.github.com';

    protected $user = '';
    protected $password = '';
    protected $authToken = '';

    protected $repo;
    protected $owner;

    public function repo($repo)
    {
        $this->repo = $repo;
        return $this;
    }

    public function owner($owner)
    {
        $this->owner = $owner;
        return $this;
    }

    public function uri($uri)
    {
        list($this->owner, $this->repo) = explode('/', $uri);
        return $this;
    }

    protected function getUri()
    {
        return $this->owner . '/' . $this->repo;
    }

    public function user($user)
    {
        $this->user = $user;
        return $this;
    }

    public function password($password)
    {
        $this->password = $password;
        return $this;
    }

    public function authToken($authToken)
    {
        $this->authToken = $authToken;
        return $this;
    }

    /**
     * Send a command to the GitHub API
     *
     * @param string $uri Contains METHOD and operation URI, separated by
     *   a space as shown in GitHub API documentation. Example: 'POST /repos/:owner/:repo/releases'
     * @param array $params
     * @param array $headers
     * @return Result
     */
    protected function sendRequest($uri, $params = [], $headers = [])
    {
        // Convert 'POST $uri' into $method and $uri.
        $method = 'POST';
        $parts = explode(' ', $uri, 2);
        if (count($parts) > 1) {
            list($method, $uri) = $parts;
        }

        $ch = curl_init();

        $replacements = [];
        if ($this->owner) {
            $replacements[':owner'] = $this->owner;
        }
        if ($this->repo) {
            $replacements[':repo'] = $this->repo;
        }
        if ($this->owner && $this->repo) {
            $replacements[':owner'] = $this->getUri();
        }

        $url = str_replace(self::GITHUB_URL . '/' . $uri, array_keys($replacements), array_values($replacements));
        if (strpos($url, ':') !== false) {
            throw new TaskException($this, "Not all replacements provided for $uri");
        }

        $this->printTaskInfo('{method} {url}', ['method' => $method, 'url' => $url]);

        if (!empty($this->authToken)) {
            $headers[] = 'Authorization: token ' . $this->authToken;
        } elseif (!empty($this->user)) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->user . ':' . $this->password);
        }

        $headers[] = "Accept: application/vnd.github.v3.raw+json";
        $headers[] = "Content-Type: application/json";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt_array(
            $ch,
            array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => $method != 'GET',
                CURLOPT_POSTFIELDS => json_encode($params),
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT => "consolidation-org/Robo"
            )
        );

        $output = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response = json_decode($output);

        $this->printTaskInfo($output);
        return new Result(
            $this,
            in_array($code, [200, 201]) ? 0 : 1,
            isset($data->message) ? $data->message : '',
            ['response' => $data]
        );
    }
}
